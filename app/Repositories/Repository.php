<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;
class Repository
{
    /**
     * 缓存时间，0为永久缓存
     *
     * @var int
     */
    protected $cacheTtl = 0;

    /**
     * 内存内缓存，避免一次请求内重复读缓存
     *
     * @var mixed|null
     */
    protected $memoryCache = null;

    /**
     * 是否使用缓存
     *
     * @var bool
     */
    protected $useCache = true;


    /**
     * 模型类名，由子类定义
     */
    protected $modelClass;

    /**
     * 保存所有子类的单例实例
     */
    protected static $instances = [];

    /**
     * 获取缓存
     *
     * @param \Closure $callback
     * @return mixed
     */
    protected function remember(\Closure $callback)
    {
        if (!is_null($this->memoryCache)) {
            return $this->memoryCache;
        }

        if (!$this->useCache || app()->environment('local')) {
            //检查是否使用缓存或者是否为本地坏境
            $this->memoryCache = $callback();
            return $this->memoryCache;
        }

        if ($this->cacheTtl > 0) {
            $this->memoryCache =  Cache::remember($this->getCacheKey(), $this->cacheTtl, $callback);
        }else{
            $this->memoryCache =  Cache::rememberForever($this->getCacheKey(), $callback);
        }

        return $this->memoryCache;
    }

    /**
     * 清理缓存
     *
     * @return void
     */
    public function forget(): void
    {
        $this->memoryCache = null;
        Cache::forget($this->getCacheKey());
    }


    /**
     * 获取缓存key，根据模型表面构建的key
     *
     * @return string
     */
    public function getCacheKey(): string
    {
        return $this->model()->getTable() . ':cache'; // 也可以自定义规则
    }

    /**
     * 获取模型实例
     */
    public function model(): \Illuminate\Database\Eloquent\Model
    {
        $modelClass = $this->getModelClass();
        return new $modelClass; // 返回模型实例
    }


    /**
     * 获取模型类
     */
    protected function getModelClass(): string
    {
        if (!$this->modelClass) {
            throw new \RuntimeException("Model class must be defined in the child repository.");
        }
        return $this->modelClass;
    }



    /**
     * make快速实例化
     */
    public static function make(...$args)
    {
        $calledClass = static::class;

        if (!isset(self::$instances[$calledClass])) {
            self::$instances[$calledClass] = new static(...$args);
        }

        return self::$instances[$calledClass];
    }

}