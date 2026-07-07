<?php

use Illuminate\Pagination\LengthAwarePaginator;

if (!function_exists('storage_url')) {
    /**
     * 获取上传的完整url
     *
     * @param $path
     * @return array|ArrayAccess|mixed
     */
    function storage_url($path)
    {
        return \Illuminate\Support\Facades\Storage::url($path);
    }
}

if (!function_exists('assetv')) {
    /**
     * 前端静态文件引入含版本
     *
     * @param $path
     * @return string
     */
    function assetv($path)
    {
        $fullPath = public_path($path);
        $version = file_exists($fullPath) ? filemtime($fullPath) : time();
        return asset($path) . '?v=' . $version;
    }
}

if (!function_exists('is_mobile')) {
    /**
     * 判断是否为mobile
     *
     * @return false|int
     */
    function is_mobile(){
        $user_agent = request()->header('user-agent');
        return preg_match("/(Android|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini)/i", $user_agent);
    }
}

if (! function_exists('template')) {
    /**
     * Get the evaluated view contents for the given view.
     *
     * Web-only（移动端走独立域名，由 RedirectDeviceMiddleware 处理跳转，不拆分 mobile 模板）。
     *
     * @param  string|null  $view
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
     * @param  array  $mergeData
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    function template($view = null, $data = [], $mergeData = []){
        $device = 'web';
        return view($device.'.'.$view,$data,$mergeData);
    }
}


if (!function_exists('paginateCollection')) {
    function paginateCollection($items, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (LengthAwarePaginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof \Illuminate\Support\Collection ? $items : collect($items);

        $paginator = new LengthAwarePaginator(
            $items->forPage($page, $perPage),
            $items->count(),
            $perPage,
            $page,
            $options
        );

        return $paginator->withPath(request()->url())->appends(request()->query())->onEachSide(1);
    }
}


if (!function_exists('get_setting')) {
    /**
     * 获取站点配置
     *
     * @param $key
     * @param $default
     * @return mixed|object
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    function get_setting($key, $default = null)
    {
        return app('site.setting')->get($key,$default);
    }
}

if (! function_exists('is_googlebot')) {
    /**
     * @return false|int
     */
    function is_googlebot(){
        $user_agent = request()->header('user-agent');
        return preg_match("/(Googlebot|Chrome-Lighthouse)/i", $user_agent);
    }
}

if (! function_exists('inject_articles_to_topics')) {
    /**
     * 将文章列表注入到主题卡片 HTML 中
     *
     * @param string $topicsHtml 原始主题卡片 HTML
     * @param array $topicsData 包含文章数据的数据
     * @return string
     */
    function inject_articles_to_topics($topicsHtml, $topicsData = [])
    {
        if (empty($topicsHtml) || empty($topicsData)) {
            return $topicsHtml;
        }

        // 创建一个 tag -> articles 的映射
        $tagArticlesMap = [];
        foreach ($topicsData as $topic) {
            if (isset($topic['tag']) && isset($topic['articles'])) {
                $tagArticlesMap[$topic['tag']] = $topic['articles'];
            }
        }

        // 使用 DOMDocument 解析 HTML
        libxml_use_internal_errors(true);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        // 不使用 LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD 以保持完整结构
        $dom->loadHTML('<?xml encoding="UTF-8">' . $topicsHtml, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $cards = $xpath->query('//section[contains(@class, "news-topic-card")]');

        foreach ($cards as $card) {
            $dataTag = $card->getAttribute('data-tag');
            if ($dataTag && isset($tagArticlesMap[$dataTag])) {
                $articles = $tagArticlesMap[$dataTag];

                // 查找 ul 元素，如果没有则创建
                $ul = $xpath->query('.//ul', $card);
                if ($ul->length > 0) {
                    $ul = $ul->item(0);
                    // 清空现有内容
                    while ($ul->hasChildNodes()) {
                        $ul->removeChild($ul->firstChild);
                    }
                } else {
                    $ul = $dom->createElement('ul');
                    $card->appendChild($ul);
                }

                // 添加文章列表项
                foreach ($articles as $article) {
                    $li = $dom->createElement('li');

                    $a = $dom->createElement('a');
                    $a->setAttribute('href', route('news.show', [$article->cate->uri, $article->id]));
                    $a->nodeValue = \Illuminate\Support\Str::limit($article->title, 30);

                    $li->appendChild($a);
                    $ul->appendChild($li);
                }
            }
        }

        // 保存并返回 HTML
        $html = $dom->saveHTML();

        // 去除多余的标签和注释
        $html = str_replace([
            '<!--?xml encoding="UTF-8"-->',
            '<?xml encoding="UTF-8"?>',
            '<!DOCTYPE html>',
        ], '', $html);

        // 移除 html 和 body 标签（如果存在）
        $html = preg_replace('/<\/?html>/i', '', $html);
        $html = preg_replace('/<\/?body>/i', '', $html);

        return trim($html);
    }
}

if (!function_exists('asset_upload')) {
    function asset_upload($path = '', $default = null)
    {
        return asset('uploads/' . $path);
    }
}

if (!function_exists('array_get')) {
    function array_get($array, $key, $default = null)
    {
        return \Illuminate\Support\Arr::get($array, $key, $default);
    }
}

if (!function_exists('getMainDomain')) {
    function getMainDomain()
    {
        $parse_url = parse_url(config('app.url'));
        return array_get($parse_url, 'host');
    }
}

if (!function_exists('is_mobile_domain')) {
    function is_mobile_domain()
    {
        $m_url = config('app.m_url');
        $parse = parse_url($m_url);

        if (array_get($parse, 'host') && request()->getHost() == array_get($parse, 'host')) {
            if (!array_get($parse, 'port')) {
                return true;
            } else {
                if (array_get($parse, 'port') != request()->getPort()) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }
}

if (!function_exists('release_token')) {
    function release_token()
    {
        $release = \App\Models\Release::orderBy('deployed_at', 'desc')->first();
        return $release ? $release->token : '';
    }
}

if (!function_exists('release_asset')) {
    function release_asset($path)
    {
        $token = release_token();
        $url = asset($path);
        if ($token) {
            $url .= '?' . $token;
        }
        return $url;
    }
}
