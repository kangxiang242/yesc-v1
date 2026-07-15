<?php

namespace App\Providers;

use App\Models\Article;
use App\Observers\ArticleObserver;
use App\Services\ConfigService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
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

        // 使用 Bootstrap 5 分页样式
        Paginator::useBootstrapFive();

        // doc/TRACKING_API.md #3：collect 端点限流 120/min（按 IP）
        RateLimiter::for('analytics-collect', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(120)
                ->by($request->header('cf-connecting-ip', $request->ip()));
        });

        // 前端 web.* 视图公共数据（平移自源项目 LayoutComposer）
        view()->composer(
            ['web.*'],
            'App\Http\Composers\LayoutComposer@all'
        );
    }
}
