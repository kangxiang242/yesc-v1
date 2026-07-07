<?php

namespace App\Repositories;

use App\Repositories\Transformer\ConfigTransformer;
use App\Models\Config;
class ConfigRepository extends Repository
{

    protected $modelClass = Config::class;


    public function all(): array
    {
        return $this->remember(function (){
            return $this->model()->pluck('content', 'name')->toArray();
        });
    }

    public function get(string $key, $default = null)
    {
        $value = $this->all()[$key] ?? $default;


        // 返回 Transformer 对象
        return new ConfigTransformer($value);
    }


}