<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteGuide extends Model
{
    protected $fillable = [
        'page_type',
        'title',
        'description',
        'item_title',
        'item_description',
        'item_image',
        'sort',
        'status',
    ];

    protected $casts = [
        'sort' => 'integer',
        'status' => 'integer',
    ];
}
