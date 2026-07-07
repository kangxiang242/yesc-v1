<?php

namespace App\Http\Middleware;

use App\Handlers\DeviceTypeHandlers;
use App\Models\AccessLog;
use Closure;
use Illuminate\Support\Arr;

class AccessLogMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        try {
            $path = '/' . ltrim($request->path(), '/');

            // 跳过后台及采集端点的访问记录
            $adminPath = config('global.admin_path', 'mgx7k9p2');
            $skipPrefixes = ['/' . $adminPath, '/api/analytics', '/observer/store'];
            foreach ($skipPrefixes as $prefix) {
                if (str_starts_with($path, $prefix)) {
                    return $response;
                }
            }

            $data = [
                'url' => $path,
                'method' => $request->method(),
                'host' => $request->getHost(),
                'referer' => Arr::get($_SERVER, 'HTTP_REFERER'),
                'ip' => $request->header('cf-connecting-ip', $request->ip()),
                'user_agent' => $request->userAgent(),
                'device' => DeviceTypeHandlers::getDevice(),
                'crawler' => DeviceTypeHandlers::getCrawler(),
                'release_token' => release_token() ?: null,
            ];
            AccessLog::create($data);
        } catch (\Exception $e) {
            // 静默处理，不影响正常请求
        }

        return $response;
    }
}
