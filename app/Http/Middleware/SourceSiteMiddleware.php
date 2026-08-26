<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SourceSiteMiddleware
{
    /**
     * 记录静态站跳转来源（source_site 归因），存入 session + cookie 供下单/留言使用。
     *
     * 静态站跳转 URL 带 ?from=<静态站域名>，本中间件：
     *  1) 记录来源（session + 明文 cookie，7 天）
     *  2) 返回 loading 中转页（JS 写 cookie + 1.5s 后跳转到去掉 from 参数的干净 URL）
     *  3) 后续请求（无 from）通过 cookie/session 读取来源，下单/留言落库
     *  4) 下单后 OrderRepository 清除 cookie + session（一次性归因）
     *
     * 无 ?from= 时兜底读 HTTP Referer（排除转化站自身域名）。
     */
    public function handle($request, Closure $next)
    {
        // 1) URL ?from= 优先（静态站跳转进入）
        if ($from = $request->input('from')) {
            $from = trim((string) $from);
            if ($from !== '' && mb_strlen($from) <= 100) {
                $this->setSource($request, $from);
                // 仅对页面请求返回 loading 中转页（排除静态资源/接口）
                if ($this->isPageRequest($request)) {
                    return $this->loadingPage($request, $from);
                }
            }
            return $next($request);
        }

        // 2) 已有来源 cookie（无 from 参数访问，如 loading 跳转后）→ 补写 session
        $cookieFrom = $request->cookie('conv_from');
        if ($cookieFrom && !session()->has('conv_from')) {
            session(['conv_from' => $cookieFrom]);
        }

        // 3) HTTP Referer 兜底
        $ref = $request->headers->get('referer');
        if ($ref) {
            $host = parse_url($ref, PHP_URL_HOST);
            if ($host && !$this->isSelfDomain($host)) {
                $this->setSource($request, $host);
            }
        }

        return $next($request);
    }

    /**
     * 判断是否为页面请求（非静态资源/接口/爬虫路径）
     */
    protected function isPageRequest(Request $request)
    {
        $path = '/' . ltrim($request->path(), '/');

        // 排除静态资源
        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|svg|ico|webp|woff2?|ttf|eot|otf|mp4|webm|zip|pdf|json|map)$/i', $path)) {
            return false;
        }
        // 排除 robots/sitemap/well-known/后台/api
        if (preg_match('#(robots\.txt|sitemap.*\.xml|\.well-known|/admin|/eni826um148sadr|/mgx7k9p2|/api/|/area/|/observer|/get711)#i', $path)) {
            return false;
        }
        return true;
    }

    /**
     * 返回 loading 中转页：JS 写 cookie + 1.5s 后跳转到去掉 from 的 URL
     */
    protected function loadingPage(Request $request, $from)
    {
        // 目标 URL：去掉 from 参数（保留其他 query）
        // 源站视角为 http（nginx 转发），强制 https，避免 HTTPS 页面被 location.replace 导航到 http 再被 CF 301 回跳
        $target = 'https://' . $request->getHttpHost() . $request->getBaseUrl() . $request->getPathInfo();
        $query = $request->query();
        unset($query['from']);
        if (!empty($query)) {
            $target .= '?' . http_build_query($query);
        }

        $escapedFrom = htmlspecialchars($from, ENT_QUOTES, 'UTF-8');
        $escapedTarget = htmlspecialchars($target, ENT_QUOTES, 'UTF-8');

        $html = <<<HTML
<!DOCTYPE html>
<html lang="zh-TW">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>載入中...</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family:-apple-system,"PingFang TC","Microsoft JhengHei",sans-serif; background:#f8fafc; display:flex; align-items:center; justify-content:center; min-height:100vh; }
  .box { text-align:center; padding:40px; }
  .spinner { width:48px; height:48px; border:4px solid #e2e8f0; border-top-color:#4f46e5; border-radius:50%; margin:0 auto 20px; animation:spin 0.8s linear infinite; }
  @keyframes spin { to { transform:rotate(360deg); } }
  .txt { color:#64748b; font-size:15px; letter-spacing:1px; }
</style>
</head>
<body>
<div class="box">
  <div class="spinner"></div>
  <div class="txt">載入中，請稍候...</div>
</div>
<script>
(function() {
  try {
    // 记录来源到 cookie（7 天，同域）
    var from = "{$escapedFrom}";
    document.cookie = "conv_from=" + encodeURIComponent(from) + "; path=/; max-age=604800; SameSite=Lax";
    // 1.5s 后跳转到去掉 from 参数的干净 URL
    setTimeout(function() {
      window.location.replace("{$escapedTarget}");
    }, 500);
  } catch (e) {
    window.location.replace("{$escapedTarget}");
  }
})();
</script>
</body>
</html>
HTML;

        return response($html)->header('Content-Type', 'text/html; charset=utf-8')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    protected function setSource($request, $value)
    {
        session(['conv_from' => $value]);
        try {
            \Illuminate\Support\Facades\Cookie::queue('conv_from', $value, 60 * 24 * 7);
        } catch (\Throwable $e) {
        }
    }

    protected function isSelfDomain($host)
    {
        $host = strtolower((string) $host);
        $self = [
            '0fficialviagra.com', '0fficialcialis.com',
            '0fficialxenical.com', '0fficiallevitra.com',
            'veshop-c1.com', 'yesc-c1.com', 'official-c1.com',
            'levitra-c1.com', 'sellevitra.com', 'cialis-store.com',
            'xenicalofficial.com', 'viagraeshop.com', 'cialiseshop.com',
            'viagra-twshop.com', 'yescialis.com',
        ];
        foreach ($self as $d) {
            if ($host === $d || str_ends_with($host, '.' . $d)) {
                return true;
            }
        }
        return false;
    }
}
