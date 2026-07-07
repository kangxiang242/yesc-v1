<?php

namespace App\Providers;

use App\Repositories\BannerRepository;
use App\Repositories\SeoRepository;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        // Share banner data for all web/mobile layouts
        View::composer(['web.*', 'mobile.*', 'components.*'], function ($view) {
            $path = '/' . trim(request()->path(), '/');
            $banner = app(BannerRepository::class)->getPageBanner($path);
            $view->with('banner', $banner);
        });

        // Share SEO data
        View::composer(['web.*', 'mobile.*'], function ($view) {
            $path = '/' . trim(request()->path(), '/');
            $seo = app(SeoRepository::class)->findPath($path);
            $layout = ['seo' => $seo];
            $view->with('layout', $layout);
        });
    }
}
