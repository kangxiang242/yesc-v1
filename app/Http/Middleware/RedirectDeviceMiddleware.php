<?php

namespace App\Http\Middleware;

use Closure;

class RedirectDeviceMiddleware
{
    public function handle($request, Closure $next)
    {
        // 站点已改响应式，不再依 UA 跳转 m 域名；
        // 仅把「非白名单 host」校正回主域名，放行后台子域名与本地开发。
        $url = config('app.url');
        if (!$url) {
            return $next($request);
        }

        $host = $request->getHost();

        // 本地开发（localhost / 127.0.0.1）直接放行，避免被校正
        if (str_contains($host, 'localhost') || str_contains($host, '127.0.0.1')) {
            return $next($request);
        }

        $allowed = [parse_url($url, PHP_URL_HOST)];
        $www = 'www.' . parse_url($url, PHP_URL_HOST);
        if (!in_array($www, $allowed, true)) {
            $allowed[] = $www;
        }
        $mUrl = config('app.m_url');
        if ($mUrl) {
            $allowed[] = parse_url($mUrl, PHP_URL_HOST);
        }
        $adminDomain = config('app.admin_domain');
        if ($adminDomain) {
            $allowed[] = parse_url($adminDomain, PHP_URL_HOST);
        }

        if (!in_array($host, $allowed, true)) {
            $n_u = rtrim($url, '/') . '/' . trim($request->path(), '/');
            return redirect($n_u);
        }

        return $next($request);
    }
}
