<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Navigation extends Model
{
    protected $fillable = [
        'parent_id', 'name', 'link', 'ico', 'sort', 'status',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'sort' => 'integer',
        'status' => 'integer',
    ];

    public function children()
    {
        return $this->hasMany(Navigation::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Navigation::class, 'parent_id');
    }
}
