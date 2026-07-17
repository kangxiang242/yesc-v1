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
| 前台路由按域名限定（yescialis.com / www / m / 本地 localhost），
| 后台（Filament）单独走 ADMIN_DOMAIN 子域名且路径为空（无后缀）。
| 两者域名互斥，避免根路径 / 冲突。
|
| 注：源项目使用 rizhou/laravel-request-log 的 request.log 中间件组，该包在
| yesc-v1 不存在，已移除；统一依赖全局已注册的 AccessLogMiddleware（别名 access.log）。
| 设备跳转 / SEO 检测交由 redirect.device 与 googlebot.checked 中间件别名处理。
|
*/

// 前台生效域名（后台子域不在此列，由 Filament 独占）
$webHost = parse_url(config('app.url'), PHP_URL_HOST);
$webDomains = array_values(array_unique(array_filter([
    $webHost,
    'www.' . $webHost,
    config('app.m_url') ? parse_url(config('app.m_url'), PHP_URL_HOST) : null,
    'localhost',
    '127.0.0.1',
])));

foreach ($webDomains as $i => $wd) {
    // Observer store - 订单/留言表单提交通知（虚拟端点返回 200，跳过 CSRF）
    Route::domain($wd)->post('/observer/store', function () {
        return response()->json(['status' => 'ok']);
    })->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
    Route::domain($wd)->any('/observer/store', function () {
        return response()->json(['status' => 'ok']);
    })->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);

    // 前端行为分析采集（跳过 CSRF，匹配前端 sendBeacon）
    // 对接 doc/TRACKING_API.md + doc/RELEASE_TOKEN.md
    Route::domain($wd)->post('/api/analytics/collect', function (\Illuminate\Http\Request $request) {
        try {
            $body = $request->json()->all();
            if (!$body || !isset($body['events'])) {
                return response()->json(['status' => 'ok']);
            }

            $platform   = $body['platform'] ?? 'web';
            $visitorId  = $body['visitor_id'] ?? null;
            $sessionId  = $body['session_id'] ?? null;
            $pageViewId = $body['page_view_id'] ?? null;
            $clientIp   = $request->header('cf-connecting-ip', $request->ip());

            // doc/TRACKING_API.md #2：props allowlist + PII 丢弃
            $propsFilter = new \App\Services\AnalyticsPropsFilter();
            // doc/TRACKING_API.md #7：双发事件规范化
            $dedup = new \App\Services\AnalyticsEventDedup();
            // doc/RELEASE_TOKEN.md #1：Release 映射派生
            $releaseMapping = new \App\Services\ReleaseMappingService();

            foreach ($body['events'] as $event) {
                if (!isset($event['event_name'])) {
                    continue;
                }

                // 双发事件 alias → canonical
                $eventName = $dedup->canonicalize($event['event_name']);

                // props allowlist 过滤（丢弃 PII）
                $props = $propsFilter->filter($event['props'] ?? []);

                // Release 映射派生（仅 page_view 派生，其余事件继承同 page_view_id 的 token）
                $releaseData = $releaseMapping->derive($props);

                \App\Models\AnalyticsEvent::create([
                    'platform'              => $platform,
                    'event_name'            => $eventName,
                    'visitor_id'            => $visitorId,
                    'session_id'            => $sessionId,
                    'page_view_id'          => $pageViewId,
                    'page_path'             => $event['page_path'] ?? null,
                    'page_type'             => $event['page_type'] ?? null,
                    'element_id'            => $event['element_id'] ?? null,
                    'props'                 => $props,
                    'ip'                    => $clientIp,
                    'user_agent'            => $request->userAgent(),
                    'host'                  => $request->getHost(),
                    'client_ts'             => $event['client_ts'] ?? null,
                    'release_token'         => $releaseData['release_token'],
                    'release_version'       => $releaseData['release_version'],
                    'release_deployed_at'   => $releaseData['release_deployed_at'],
                    'release_status'        => $releaseData['release_status'],
                    'asset_token_status'    => $releaseData['asset_token_status'],
                ]);
            }
        } catch (\Exception $e) {
            // 静默处理（前端 sendBeacon 不读响应）
        }

        return response()->json(['status' => 'ok']);
    })->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class])
      ->middleware('throttle:analytics-collect');

    // API / 静态资源类路由（无需设备跳转重定向，返回 JSON 或资源）
    Route::domain($wd)->group(function () {
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
    Route::domain($wd)->middleware(['redirect.device', 'googlebot.checked'])->group(function () {
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

    // 兜底页面 / 文章路由（放最后）；news.show 仅在主域命名，避免 route:cache 同名冲突
    Route::domain($wd)->middleware(['redirect.device', 'googlebot.checked'])->group(function () use ($i) {
        Route::get('{uri}', [PageController::class, 'index']);
        if ($i === 0) {
            Route::get('{uri}/{id}.html', [NewsController::class, 'show'])->name('news.show');
        } else {
            Route::get('{uri}/{id}.html', [NewsController::class, 'show']);
        }
        Route::get('{uri}/{id}', [NewsController::class, 'show']);
    });
}

// Admin: wangEditor image upload（Filament 富文本上传用，路径独立，不限域名）
Route::post('/admin/upload/wang-editor/image', [WangEditorUploadController::class, 'upload'])
    ->name('admin.wang-editor.upload')
    ->middleware('web');
