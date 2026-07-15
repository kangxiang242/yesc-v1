<?php

use App\Exceptions\ValidationFailedException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\AccessLogMiddleware::class);
        // 必须放在全局栈：在 web group 的 AddQueuedCookiesToResponse 写入 Set-Cookie 之后清理
        $middleware->append(\App\Http\Middleware\EnforceCacheHeadersMiddleware::class);
        $middleware->alias([
            'redirect.device' => \App\Http\Middleware\RedirectDeviceMiddleware::class,
            'googlebot.checked' => \App\Http\Middleware\GooglebotChecked::class,
            'access.log' => \App\Http\Middleware\AccessLogMiddleware::class,
            'defend' => \App\Http\Middleware\DefendMiddleware::class,
            'cacheable.public' => \App\Http\Middleware\CacheableMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationFailedException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => [],
            ], 422);
        });
    })->create();
