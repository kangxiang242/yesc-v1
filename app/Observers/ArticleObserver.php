<?php

namespace App\Observers;

use App\Models\Article;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ArticleObserver
{
    public function saved(Article $article)
    {
        // 1. 清理 Nginx FastCGI 缓存
        try {
            exec('rm -rf /var/cache/nginx/viagra-twshop/*');
            exec('nginx -s reload 2>/dev/null');
        } catch (\Exception $e) {
            Log::warning('ArticleObserver: FastCGI cache clear failed', [
                'error' => $e->getMessage()
            ]);
        }

        // 2. 清理 Cloudflare 缓存（只清这一篇文章的 URL + 分类列表页）
        $zoneId = config('services.cloudflare.zone_id');
        $apiToken = config('services.cloudflare.api_token');

        if ($zoneId && $apiToken && $article->cate) {
            $urls = array_filter([
                url($article->cate->uri . '/' . $article->id . '.html'),
                url($article->cate->uri),
            ]);

            try {
                Http::withToken($apiToken)
                    ->timeout(5)
                    ->post("https://api.cloudflare.com/client/v4/zones/{$zoneId}/purge_cache", [
                        'files' => $urls,
                    ]);
            } catch (\Exception $e) {
                Log::warning('ArticleObserver: CF purge failed', [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }
}
