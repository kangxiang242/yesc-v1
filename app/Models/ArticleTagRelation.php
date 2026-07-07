<?php

namespace App\Models;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
class ArticleTagRelation extends Model
{
    use HasFactory;
    protected $table = 'article_tag_relations';

    protected $fillable = ['article_id', 'tag_id'];

    /**
     * 关联标签
     */
    public function tag()
    {
        return $this->belongsTo(ArticleTag::class, 'tag_id');
    }

    /**
     * 关联文章
     */
    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }
}
