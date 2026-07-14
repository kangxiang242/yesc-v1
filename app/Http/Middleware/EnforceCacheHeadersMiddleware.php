<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

/**
 * 全局响应中间件：对被 CacheableMiddleware 标记的 GET 2xx 响应，
 * 剥除所有 Set-Cookie 并设置公共缓存响应头，使 Cloudflare 可缓存。
 *
 * 站點已響應式，桌面 / 移動端同頁，均可啟用公共緩存。
 *
 * 必须挂为全局中间件（而非路由中间件），以确保在
 * AddQueuedCookiesToResponse 把 cookies 写入响应之后再清理。
 */
class EnforceCacheHeadersMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (
            $request->attributes?->get('_cacheable_public')
            && $request->isMethod('GET')
            && $response->getStatusCode() === 200
        ) {
            $this->stripCookies($response);
            // 使用 Symfony 的缓存 API，确保旧指令被完全替换
            $response->setCache([
                'public' => true,
                'max_age' => 600,
                's_maxage' => 600,
            ]);
        }

        return $response;
    }

    /**
     * 清掉 Response 上所有 Cookie（XSRF-TOKEN、{session}_session 等）。
     * Laravel 的 cookies 通过 Symfony HeaderBag::getCookies() 管理，
     * removeCookie 按 name+path+domain 精确移除；并兜底删除裸 Set-Cookie header。
     */
    protected function stripCookies(Response $response): void
    {
        /** @var Cookie $cookie */
        foreach ($response->headers->getCookies() as $cookie) {
            $response->headers->removeCookie(
                $cookie->getName(),
                $cookie->getPath(),
                $cookie->getDomain()
            );
        }

        $response->headers->remove('Set-Cookie');
    }
}