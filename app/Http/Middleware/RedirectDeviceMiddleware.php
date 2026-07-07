<?php

namespace App\Http\Middleware;

use App\Handlers\DeviceTypeHandlers;
use Closure;

class RedirectDeviceMiddleware
{
    public function handle($request, Closure $next)
    {
        $is_mobile = DeviceTypeHandlers::isMobile();
        if ($is_mobile) {
            $url = config('app.m_url');
        } else {
            $url = config('app.url');
        }
        if ($url) {
            $parse_url = parse_url($url);
            if (isset($parse_url['host']) && $parse_url['host'] != $request->getHost()) {
                $n_u = $url . '/' . trim($request->path(), '/');
                return redirect($n_u);
            }
        }

        return $next($request);
    }
}
