<?php

namespace App\Models;

use App\Models\Traits\Resizable;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use Resizable;

    protected $casts = [
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'delete_at'  => 'datetime',
        'release_at' => 'datetime',
    ];

    public function cate()
    {
        return $this->belongsTo(ArticleCate::class, 'article_cate_id');
    }

    public function tags()
    {
        return $this->belongsToMany(ArticleTag::class, 'article_tag_relations', 'article_id', 'tag_id')
            ->where('article_tags.status', 1)
            ->orderBy('article_tags.sort', 'asc');
    }

    public function getTagIdsAttribute()
    {
        return $this->tags()->pluck('article_tags.id')->toArray();
    }

    public function scopeWithTagIds($query, $tagIds)
    {
        if ($tagIds && is_array($tagIds) && !empty($tagIds)) {
            return $query->whereHas('tags', function ($q) use ($tagIds) {
                $q->whereIn('article_tag_relations.tag_id', $tagIds);
            });
        }
        return $query;
    }

    public function thumbnail($type, $attribute = 'img', $disk = null)
    {
        if (! isset($this->attributes[$attribute])) {
            $image = $attribute;
        } else {
            $image = $this->attributes[$attribute];
        }

        $thumbnail = $this->getThumbnailPath($image, $type);

        return file_exists(public_path('storage/'.$thumbnail)) ? $thumbnail : $image;
    }
}
