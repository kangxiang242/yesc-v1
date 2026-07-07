<?php

namespace App\Repositories;

use App\Models\Article;

class ArticleRepository extends Repository
{

    protected $modelClass = Article::class;


    public function all()
    {
        return $this->remember(function (){
            return $this->model()->with('cate')->select('id','article_cate_id','title','brief','img','img_alt','release_at','read_num','real_read_num','updated_at','status','content')->where('status',1)->orderBy('sort','asc')->get();
        });
    }

    public function random($limit = 6){
        return $this->all()->random($limit);
    }

    public function top($limit = 6){
        return $this->all()->sortByDesc('is_recommend')->take($limit);
    }

    public function newNews($limit = 6){
        return $this->all()->sortByDesc('release_at')->take($limit);
    }


    public function find($id){
        return $this->all()->where('id',$id)->first();
    }

    public function getNext($id,$cate_id)
    {
        $articles = $this->all()->where('article_cate_id',$cate_id)->values();
        $count = $articles->count();

        if ($count === 0) {
            return null;
        }

        $index = $articles->search(fn ($item) => $item->id == $id);

        if ($index === false) {
            return null;
        }

        // 关键：取模
        $nextIndex = ($index + 1) % $count;

        return $articles[$nextIndex];
    }

    public function getPrev($id,$cate_id)
    {
        $articles = $this->all()->where('article_cate_id',$cate_id)->values();
        $count = $articles->count();

        if ($count === 0) {
            return null;
        }

        $index = $articles->search(fn ($item) => $item->id == $id);

        if ($index === false) {
            return null;
        }

        // PHP 负数取模会是负数，所以要 + $count
        $prevIndex = ($index - 1 + $count) % $count;

        return $articles[$prevIndex];
    }




}