<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * 标记当前路由为「公共可缓存」。
 *
 * 本中间件本身不做任何响应改写，仅向 request 注入一个属性，
 * 供全局 EnforceCacheHeadersMiddleware 在响应阶段做最终的
 * Set-Cookie 清理与 Cache-Control 覆写。
 *
 * 注意：响应改写必须放在全局中间件中执行，因为
 * StartSession / AddQueuedCookiesToResponse 属于 web middleware group，
 * 比路由中间件更外层，会在路由中间件之后才把 Set-Cookie 写入响应。
 */
class CacheableMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $request->attributes->set('_cacheable_public', true);

        return $next($request);
    }
}