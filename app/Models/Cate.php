<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cate extends Model
{
    protected $fillable = [
        'pid', 'name', 'status', 'sort',
    ];

    protected $parentColumn = 'pid';
    protected $titleColumn = 'name';
    protected $orderColumn = 'sort';

    public function sub()
    {
        return $this->hasMany(Cate::class, 'pid', 'id')->orderBy('sort', 'asc');
    }

    public function parent()
    {
        return $this->hasOne(Cate::class, 'id', 'pid');
    }

    public function product(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_cates', 'product_id', 'cate_id');
    }
}
