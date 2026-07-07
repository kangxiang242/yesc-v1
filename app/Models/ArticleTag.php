<?php

namespace App\Models;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Str;
class ArticleTag extends Model
{
    use HasFactory;
    protected $table = 'article_tags';

    protected $fillable = ['name', 'slug', 'description', 'cat_ids', 'color', 'sort', 'status'];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($tag) {
            // 如果 slug 为空，自动从 name 生成
            if (empty($tag->slug)) {
                $tag->slug = Str::slug($tag->name);
            }
        });
    }

    protected $casts = [
        'status' => 'integer',
        'sort' => 'integer',
        'cat_ids' => 'array',  // 自动转换为数组
    ];

    /**
     * 关联文章
     */
    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_tag_relations', 'tag_id', 'article_id')
            ->where('status', 1)
            ->orderBy('sort', 'asc');
    }

    /**
     * 关联文章分类
     */
    public function categories()
    {
        return $this->belongsToMany(ArticleCate::class, 'article_tag_categories', 'tag_id', 'cate_id');
    }

    /**
     * 获取关联的分类ID数组
     */
    public function getCatIdsArrayAttribute()
    {
        return $this->cat_ids ?? [];
    }

    /**
     * 判断标签是否属于某个分类
     */
    public function belongsToCategory($cateId)
    {
        return in_array($cateId, $this->cat_ids ?? []);
    }

    /**
     * 状态文本
     */
    public function getStatusTextAttribute()
    {
        return $this->status === 1 ? '启用' : '禁用';
    }
}
