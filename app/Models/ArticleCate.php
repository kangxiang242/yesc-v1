<?php

namespace App\Models;
    use Illuminate\Database\Eloquent\Model;
class ArticleCate extends Model
{

    /**
     * 获取博客文章的评论
     */
    public function article()
    {
        return $this->hasMany(Article::class)->select(['id','article_cate_id','title','brief','img','img_alt','release_at','read_num','real_read_num','updated_at','status']);
    }

}
