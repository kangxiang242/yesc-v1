<?php

namespace App\Models;

use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    protected $fillable = [
        'name', 'code', 'img', 'm_img', 'subtitle', 'price', 'market_price',
        'status', 'is_stock', 'sort', 'label', 'describe', 'collect_code', 'added', 'quantity',
    ];

    protected $casts = [
        'added' => 'json',
    ];

    protected static function booted(): void
    {
        $clearCache = static function () {
            ProductRepository::make()->forget();
        };

        static::saved($clearCache);
        static::deleted($clearCache);
    }

    public function attr()
    {
        return $this->hasMany(ProductAttr::class, 'product_id', 'id');
    }

    public function getSubnameAttribute(): ?string
    {
        return $this->subtitle;
    }

    public function getLabelTagsAttribute(): array
    {
        if (empty($this->label)) {
            return [];
        }

        return array_values(array_filter(array_map(
            'trim',
            explode('|', (string) $this->label)
        )));
    }

    public function getDiscountPercentAttribute()
    {
        if ($this->market_price && $this->price && $this->market_price > $this->price) {
            return round(($this->market_price - $this->price) / $this->market_price * 100);
        }
        return 0;
    }

    public function getPricePerPillAttribute()
    {
        if ($this->price && $this->quantity && $this->quantity > 0) {
            return round($this->price / $this->quantity / 4);
        }
        return 0;
    }
}
