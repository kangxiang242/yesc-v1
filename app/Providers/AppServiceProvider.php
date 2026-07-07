<?php

namespace App\Providers;

use App\Models\Article;
use App\Observers\ArticleObserver;
use App\Services\ConfigService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register cache.config binding (used in views)
        $this->app->singleton('cache.config', function () {
            return new class {
                public function get($key, $default = null) {
                    return ConfigService::get($key, $default);
                }
            };
        });

        // 站点配置绑定，get_setting() helper 依赖此单例（平移自源项目）
        $this->app->singleton('site.setting', function () {
            return new \App\Repositories\ConfigRepository();
        });
    }

    public function boot(): void
    {
        Article::observe(ArticleObserver::class);

        // 前端 web.* 视图公共数据（平移自源项目 LayoutComposer）
        view()->composer(
            ['web.*'],
            'App\Http\Composers\LayoutComposer@all'
        );
    }
}
