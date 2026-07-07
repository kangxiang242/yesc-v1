<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    public static function getUriLabel()
    {
        $uris = [
            '/' => '首頁',
            'product' => '產品列表',
            'goods/{id}' => '產品詳情',
            'effect' => '使用心得',
            'health' => '兩性健康',
            'check' => '訂單查詢',
            'message' => '客服協助',
            'promise' => '訂購指南',
            'side-effects' => '副作用',
            'contraindications' => '禁忌',
            'usage' => '使用方法',
        ];

        return collect($uris);
    }
}
