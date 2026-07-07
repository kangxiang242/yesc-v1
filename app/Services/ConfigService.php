<?php


namespace App\Services;


use App\Models\Config;
use Illuminate\Support\Facades\Cache;
class ConfigService
{

    private static $key = "config";

    public static function cache(){
        $config = Config::all();
        foreach($config as $item){
            Cache::put(self::$key.":{$item->name}", $item->content);
        }
    }

    /**
     * 获取配置值
     * @param $key
     * @param null $default
     * @return mixed
     */
    public static function get($key,$default=null){

        $cacheKey = self::$key.':'.$key;
        $content = Cache::get($cacheKey);

        if(is_null($content)){
            $config = Config::where('name',$key)->first();

            if($config){
                $content = $config->content;
                Cache::put($cacheKey, $content);
            }
        }

        return is_null($content)?$default:$content;
    }
}
