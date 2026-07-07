<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Config;
use App\Repositories\BannerDescRepository;
use App\Repositories\ProductRepository;
use App\Services\SitemapService;
use Illuminate\Support\Facades\Storage;


class ApiController extends Controller
{

    public function robots(){
        $config = Config::where('name','robots')->first();
        if($config){
            return response($config->content)->header('Content-type','text/plain');
        }else{
            return response('')->header('Content-type','text/plain');
        }
    }

    public function sitemap(){
        $xml = app(SitemapService::class)->generate();
        return response($xml)->header('Content-type','text/xml');
    }

    public function googleVerify($str){
        $fullname = 'google'.$str.'.html';
        if(Storage::disk('public')->exists('google-verify/'.$fullname)){
            return Storage::disk('public')->get('google-verify/'.$fullname);
        }
        abort(404);

    }

    public function generalBannerFragment(
        ProductRepository $productRepository,
        BannerDescRepository $bannerDescRepository
    ) {
        $randomProduct = $productRepository->all();
        $globalBanner = $bannerDescRepository->all();

        // 无商品/无横幅数据（数据库尚未 seeding）时直接返回空片段，避免渲染报错
        if ($randomProduct->isEmpty() || $globalBanner->isEmpty()) {
            return response()->json(['html' => '']);
        }

        $randomProduct = $randomProduct->random();
        $globalBanner = $globalBanner->random();
        $globalBanner->img = $this->pickGlobalBannerImage();

        $html = view('components.general-banner', [
            'random_product' => $randomProduct,
            'global_banner' => $globalBanner,
        ])->render();

        return response()->json([
            'html' => $html,
        ]);
    }

    protected function pickGlobalBannerImage()
    {
        $globalBanners = get_setting('global_banners')->toArray();
        if (!$globalBanners) {
            return '';
        }
        return storage_url(data_get($globalBanners, array_rand($globalBanners)));
    }
}
