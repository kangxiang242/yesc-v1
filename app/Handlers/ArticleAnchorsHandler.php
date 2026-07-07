<?php


namespace App\Handlers;


use App\Models\Article;
use App\Models\Product;
use Illuminate\Support\Arr;

class ArticleAnchorsHandler
{
    /**
     * 自动锚文本，a h1 h2 h3 不做锚文本处理
     * @param $str
     * @param $anchors
     * @return string|string[]|null
     */
    public function setAnchors($str,$anchors)
    {

        if(!$anchors){
            return $str;
        }

        $rule = "/<img.*>/";
        //先把img排除掉,并且将其存为一个数组
        preg_match_all($rule, $str, $matches);

        $str_without_alt = preg_replace($rule, 'Its_Just_A_Mark', $str);


        //锚处理
        foreach ($anchors as $anchor) {
            $rule = "/".$anchor['name']."(?!((?!<(a|h1|h2|h3|h4|h5|h6|img)\b)[\s\S])*<\/(a|h1|h2|h3|h4|h5|h6|img)>)/";
            $href = '<a target="_blank" href="'.$anchor['url'].'" class="seo-anchor">'.$anchor['name'].'</a>';
            $str_without_alt = preg_replace($rule, $href, $str_without_alt,1); //无限

        }


        $new_content = $str_without_alt;
        //将img加上去
        if(isset($matches[0])){

            foreach ($matches[0] as $alt_content) {

                $new_content = preg_replace('/Its_Just_A_Mark/',$alt_content,$str_without_alt,1);

            }
        }

        return $new_content;
    }

    /**
     * 文章短代码生成
     * @param $str
     * @param null $id
     * @return string|string[]
     */
    public function relatedArticle($str,$id=null){

        $holder = $this->related('article',$str);

        foreach($holder as $item){

            $rule = $item['full'];

            $html = "<div class='article-related'><h2>為您推薦以下文章：</h2><div class='article-related-list'>";

            if($item['ids'] == 0){
                if($id){
                    $article = Article::inRandomOrder()->where('id','<>',$id)->limit(3)->get();
                }else{
                    $article = Article::inRandomOrder()->limit(3)->get();
                }

            }else{
                $ids = explode(',',$item['ids']);
                $article = Article::whereIn('id',$ids)->get();
            }
            foreach($article as $art){
                $html .= "<p><a target='_blank' href='".url('news/'.$art->id)."'>".$art->title."</a></p>";
            }
            $html .= "</div></div>";
            $str = str_replace($rule, $html, $str);

        }
        return $str;

    }

    /**
     * 产品短代码生成
     * @param $str
     * @return string|string[]
     */
    public function relatedProduct($str){
        $holder = $this->related('product',$str);

        foreach($holder as $item){

            $rule = $item['full'];
            $id = (integer)$item['ids'];

            if($id <= 0){
                $product = Product::where('status',1)->inRandomOrder()->first();
            }else{

                $product = Product::find($item['ids']);
            }

            $html = view('render.article.product',compact('product'))->render();

            $str = str_replace($rule, trim($html), $str);


        }
        return $str;
    }

    /**
     * 正则匹配短代码
     * @param $label
     * @param $content
     * @return array
     */
    private function related($label,$content){
        $rule = "/\[\*\*".$label.":(.*?)\*\*\]/i";
        preg_match_all($rule, $content, $matches);

        $holder = [];
        foreach(Arr::get($matches,0) as $k=>$item){
            $holder[] = [
                'full'=>$item,
                'ids'=>Arr::get($matches,"1.".$k),
            ];
        }
        return $holder;

    }


}
