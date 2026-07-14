<?php

namespace App\Http\Middleware;

use Closure;

class RedirectDeviceMiddleware
{
    public function handle($request, Closure $next)
    {
        // 站點已改響應式，不再依 UA 跳轉 m 域名；僅校正非 APP_URL 的 host（如 127.0.0.1 → localhost）
        $url = config('app.url');
        if ($url) {
            $parse_url = parse_url($url);
            if (isset($parse_url['host']) && $parse_url['host'] != $request->getHost()) {
                $n_u = rtrim($url, '/') . '/' . trim($request->path(), '/');
                return redirect($n_u);
            }
        }

        return $next($request);
    }
}
