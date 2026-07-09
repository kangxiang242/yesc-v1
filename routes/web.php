<?php

use App\Http\Controllers\Admin\WangEditorUploadController;
use App\Http\Controllers\Api\RegionStoreController;
use App\Http\Controllers\BuyerMessageController;
use App\Http\Controllers\Web\ApiController;
use App\Http\Controllers\Web\AreaController;
use App\Http\Controllers\Web\IndexController;
use App\Http\Controllers\Web\MessageController;
use App\Http\Controllers\Web\NewsController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\PageController;
use App\Http\Controllers\Web\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| 前端路由平移自 yescialis（Laravel 10）源项目，覆盖参考项目 twshop 占位实现。
| 注：源项目使用 rizhou/laravel-request-log 的 request.log 中间件组，该包在
| yesc-v1 不存在，已移除；统一依赖全局已注册的 AccessLogMiddleware（别名 access.log）。
| 设备跳转 / SEO 检测交由 redirect.device 与 googlebot.checked 中间件别名处理。
|
*/

// Observer store - 订单/留言表单提交通知（虚拟端点返回 200，跳过 CSRF）
Route::post('/observer/store', function () {
    return response()->json(['status' => 'ok']);
})->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
Route::any('/observer/store', function () {
    return response()->json(['status' => 'ok']);
})->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// 前端行为分析采集（跳过 CSRF，匹配前端 sendBeacon）
Route::post('/api/analytics/collect', function (\Illuminate\Http\Request $request) {
    try {
        $body = $request->json()->all();
        if (!$body || !isset($body['events'])) {
            return response()->json(['status' => 'ok']);
        }

        $platform = $body['platform'] ?? 'web';
        $visitorId = $body['visitor_id'] ?? null;
        $sessionId = $body['session_id'] ?? null;
        $pageViewId = $body['page_view_id'] ?? null;

        foreach ($body['events'] as $event) {
            if (!isset($event['event_name'])) {
                continue;
            }

            $props = isset($event['props']) ? $event['props'] : [];
            $releaseToken = null;
            if (is_array($props) && isset($props['html_token'])) {
                $releaseToken = $props['html_token'];
            }

            \App\Models\AnalyticsEvent::create([
                'platform' => $platform,
                'event_name' => $event['event_name'],
                'visitor_id' => $visitorId,
                'session_id' => $sessionId,
                'page_view_id' => $pageViewId,
                'page_path' => $event['page_path'] ?? null,
                'page_type' => $event['page_type'] ?? null,
                'element_id' => $event['element_id'] ?? null,
                'props' => $props,
                'ip' => $request->header('cf-connecting-ip', $request->ip()),
                'user_agent' => $request->userAgent(),
                'host' => $request->getHost(),
                'client_ts' => $event['client_ts'] ?? null,
                'release_token' => $releaseToken,
            ]);
        }
    } catch (\Exception $e) {
        // 静默处理
    }

    return response()->json(['status' => 'ok']);
})->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

// Admin: wangEditor image upload（Filament 富文本上传用，保留）
Route::post('/admin/upload/wang-editor/image', [WangEditorUploadController::class, 'upload'])
    ->name('admin.wang-editor.upload')
    ->middleware('web');

// API / 静态资源类路由（无需设备跳转重定向，返回 JSON 或资源）
Route::group([], function () {
    Route::get('/area/city', [AreaController::class, 'getCity']);
    Route::get('/area/county', [AreaController::class, 'getCounty']);
    Route::get('/area/road', [AreaController::class, 'getRoad']);
    Route::get('/area/shop', [AreaController::class, 'getShop']);
    Route::get('/api/general-banner-fragment', [ApiController::class, 'generalBannerFragment']);
    Route::get('/api/regionstore/linkage', [RegionStoreController::class, 'linkage']);
    Route::get('/api/regionstore/proxy', [RegionStoreController::class, 'proxy']);
    Route::get('/robots.txt', [ApiController::class, 'robots']);
    Route::get('/sitemap.xml', [ApiController::class, 'sitemap']);
    Route::get('google{str}.html', [ApiController::class, 'googleVerify']);

    // 购买消息相关 API（首页实时购买通知）
    Route::get('/api/buyer-message/box-buyers', [BuyerMessageController::class, 'getBoxBuyers']);
    Route::get('/api/buyer-message/next-message', [BuyerMessageController::class, 'getNextMessage']);
    Route::post('/api/buyer-message/confirm', [BuyerMessageController::class, 'confirmMessage']);
    Route::post('/api/buyer-message/increment', [BuyerMessageController::class, 'incrementBuyer']);
});

// 主前端路由（设备跳转 + SEO 检测）
Route::middleware(['redirect.device', 'googlebot.checked'])->group(function () {
    Route::get('/', [IndexController::class, 'index']);
    Route::any('/check', [OrderController::class, 'check']);
    Route::get('/check/{no}', [OrderController::class, 'checking']);
    Route::get('/order/{no}', [OrderController::class, 'checking']);

    Route::get('/product', [ProductController::class, 'index']);
    Route::get('/goods/{id}', [ProductController::class, 'show']);

    Route::get('/shopping/{id}', [OrderController::class, 'checkout']);
    Route::post('/order', [OrderController::class, 'store']);

    Route::get('/message', [MessageController::class, 'index']);
    Route::post('/message', [MessageController::class, 'store']);

    Route::get('/area', [AreaController::class, 'get']);
    Route::get('/get711', [AreaController::class, 'getShop']);
});

// 兜底页面 / 文章路由（放最后）
Route::middleware(['redirect.device', 'googlebot.checked'])->group(function () {
    Route::get('{uri}', [PageController::class, 'index']);
    Route::get('{uri}/{id}.html', [NewsController::class, 'show'])->name('news.show');
    Route::get('{uri}/{id}', [NewsController::class, 'show']);
});
