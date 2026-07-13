<?php

namespace App\Models;
    use Illuminate\Database\Eloquent\Model;
class OrderProduct extends Model
{
    protected $fillable = [
        'order_id','product_id','product_name','product_img','number','unit_price','total_price','product'
    ];

    protected $casts = [
        'product' => 'json', // 声明json类型
    ];
}
