<?php


namespace App\Http\Controllers\Web;


use App\Http\Controllers\Controller;
use App\Repositories\ArticleCategoryRepository;
use App\Repositories\ArticleRepository;
use App\Repositories\PageRepository;
use App\Models\Faq;
use DOMDocument;
use DOMXPath;


class PageController extends Controller
{
    private $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function index($uri){
        // 排除后台管理员路径，避免拦截 Filament Admin 路由
        if ($uri === config('global.admin_path')) {
            abort(404);
        }

        $page = PageRepository::make()->findToUri($uri);
        
        // 如果是 effect、health 或 sideeffects，使用 news 模板
        if ($uri === 'effect' || $uri === 'health' || $uri === 'sideeffects') {
            return $this->news($uri, $page);
        }

        if(!$page){
            return $this->news($uri);
        }

        // 解析 content 字段，提取不同区块
        $contentParts = $this->parsePageSections($page->content);
        
        // 处理主内容（带目录）
        $mainContent = $contentParts['main'] ?? $page->content;
        [$content, $toc] = $this->parsePageContentWithToc((string) $mainContent);
        $page->content = $content;

        // 提取其他区块（设置属性，不修改数据库）
        $page->topics_title = $contentParts['topics_title'] ?? null;
        $page->topics_html = $contentParts['topics'] ?? null;
        $page->topics_data = $contentParts['topics_data'] ?? [];
        $page->bottom_html = $contentParts['bottom'] ?? null;

        $faqs = Faq::where('status', 1)
            ->where('uri', $uri)
            ->orderBy('sort')
            ->get();

        return template('page', compact('page', 'faqs', 'toc'));
    }

    public function news($uri, $page = null){
        $cate = ArticleCategoryRepository::make()->all()->where('uri',$uri)->first();

        if(!$cate){
            abort(404);
        }

        // 获取当前分类下的所有标签（用于标签筛选）
        $filterTags = $this->getCategoryFilterTags($cate->id);

        // 处理标签筛选
        $tagSlug = request()->query('tag');
        $activeTag = null;
        $articlesQuery = ArticleRepository::make()->all()->where('article_cate_id', $cate->id);

        if ($tagSlug && !empty($filterTags)) {
            // 查找标签
            $activeTag = collect($filterTags)->firstWhere('slug', $tagSlug);
            if ($activeTag) {
                // 过滤：只保留有关联该标签的文章
                $tagArticleIds = $activeTag['article_ids'] ?? [];
                $articlesQuery = $articlesQuery->filter(function ($article) use ($tagArticleIds) {
                    return in_array($article->id, $tagArticleIds);
                });
            }
        }

        $news = paginateCollection($articlesQuery, 6);
        $hot = ArticleRepository::make()->random(1);
        $topNews = $news->first();
        
        // 判断是否为effect模式
        $isEffectMode = request()->is('effect*');

        // 处理 Page 内容（如果有）
        $pageContent = null;
        $topicsTags = [];
        $bottomHtml = null;

        if ($page) {
            $contentParts = $this->parsePageSections($page->content);

            // 提取主内容（主题分类下方的编辑区块）
            if (isset($contentParts['main'])) {
                [$pageContent] = $this->parsePageContentWithToc($contentParts['main']);
            }

            // 根据页面 uri 获取标签数据
            $topicsTags = $this->getTopicsTagsByUri($page->uri);

            // 提取底部内容
            $bottomHtml = $contentParts['bottom'] ?? null;
        }

        return template('news.index', compact(
            'news',
            'cate',
            'hot',
            'page',
            'pageContent',
            'topicsTags',
            'bottomHtml',
            'isEffectMode',
            'topNews',
            'filterTags',
            'activeTag',
            'tagSlug'
        ));
    }

    private function parsePageContentWithToc(string $html): array
    {
        if (trim($html) === '') {
            return ['', []];
        }

        libxml_use_internal_errors(true);

        $dom = new DOMDocument('1.0', 'UTF-8');
        $wrapped = '<div id="__page-root">' . $html . '</div>';
        $dom->loadHTML(
            mb_convert_encoding($wrapped, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        $xpath = new DOMXPath($dom);
        /* $nodes = $xpath->query('//h2 | //h3'); */
        $nodes = $xpath->query('//h2');

        $toc = [];
        $currentH2Index = -1;

        foreach ($nodes as $index => $node) {
            $tag = strtolower($node->nodeName);
            $text = trim($node->textContent);

            if (!$node->hasAttribute('id')) {
                $id = $this->slug($text) ?: 'section-' . $index;
                $node->setAttribute('id', $id);
            } else {
                $id = $node->getAttribute('id');
            }

            if ($tag === 'h2') {
                $toc[] = [
                    'id' => $id,
                    'title' => $text,
                    'children' => [],
                ];
                $currentH2Index++;
                continue;
            }

            /* if ($tag === 'h3' && $currentH2Index >= 0) {
                $toc[$currentH2Index]['children'][] = [
                    'id' => $id,
                    'title' => $text,
                ];
            } */
        }

        $content = '';
        $root = $dom->getElementById('__page-root');
        if ($root) {
            foreach ($root->childNodes as $child) {
                $content .= $dom->saveHTML($child);
            }
        } else {
            $content = $dom->saveHTML();
        }

        return [$content, $toc];
    }

    private function slug(string $text): string
    {
        $slug = mb_strtolower($text);
        $slug = preg_replace('/[^\p{L}\p{N}]+/u', '-', $slug);

        return trim($slug, '-');
    }

    /**
     * 解析页面内容，提取不同区块
     * 支持 HTML 注释分隔：
     * - <!-- page-header -->...<!-- page-header end -->
     * - <!-- page-main-content -->...<!-- page-main-content end -->
     * - <!-- page-topics-section -->...<!-- page-topics-section end -->
     * - <!-- page-bottom-content -->...<!-- page-bottom-content end -->
     */
    private function parsePageSections($content) {
        $parts = [];
        
        // 提取页面头部
        if (preg_match('/<!--\s*page-header\s*-->(.*?)<!--\s*page-header\s*end\s*-->/s', $content, $matches)) {
            $parts['header'] = $matches[1];
        }
        
        // 提取主内容
        if (preg_match('/<!--\s*page-main-content\s*-->(.*?)<!--\s*page-main-content\s*end\s*-->/s', $content, $matches)) {
            $parts['main'] = $matches[1];
        } else {
            // 如果没有注释，全部作为主内容
            $parts['main'] = $content;
        }
        
        // 提取主题分类
        if (preg_match('/<!--\s*page-topics-section\s*-->(.*?)<!--\s*page-topics-section\s*end\s*-->/s', $content, $matches)) {
            $parts['topics'] = $matches[1];
            $parts['topics_data'] = $this->parseTopicCards($parts['topics']);
        }
        
        // 提取底部内容
        if (preg_match('/<!--\s*page-bottom-content\s*-->(.*?)<!--\s*page-bottom-content\s*end\s*-->/s', $content, $matches)) {
            $parts['bottom'] = $matches[1];
        }
        
        return $parts;
    }

    private function parseTopicCards($topicsHtml)
    {
        $topics = [];

        // 使用 DOMDocument 解析 HTML
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">' . $topicsHtml);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $cards = $xpath->query('//section[contains(@class, "news-topic-card")]');

        foreach ($cards as $card) {
            $topic = [
                'title' => '',
                'description' => '',
                'tag' => '',
                'articles' => []
            ];

            // 获取标题
            $h2 = $xpath->query('.//h2', $card);
            if ($h2->length > 0) {
                $topic['title'] = trim($h2->item(0)->textContent);
            }

            // 获取描述
            $p = $xpath->query('.//p', $card);
            if ($p->length > 0) {
                $topic['description'] = trim($p->item(0)->textContent);
            }

            // 获取标签
            $dataTag = $card->getAttribute('data-tag');
            if ($dataTag) {
                $topic['tag'] = trim($dataTag);
                $topic['articles'] = $this->getArticlesByTag($dataTag);
            }

            $topics[] = $topic;
        }

        return $topics;
    }

    private function getArticlesByTag($tagId, $cateId = null)
    {
        // 查找对应的标签
        $tag = \App\Models\ArticleTag::where('id', $tagId)
            ->where('status', 1)
            ->first();

        if (!$tag) {
            return [];
        }

        // 获取该标签下的所有文章
        $query = $tag->articles()
            ->where('status', 1)
            ->orderBy('release_at', 'desc');

        // 如果指定了分类，只返回该分类下的文章
        if ($cateId) {
            $query->where('article_cate_id', $cateId);
        }

        return $query->get();
    }

    /**
     * 获取当前分类下的所有标签（用于标签筛选栏）
     */
    private function getCategoryFilterTags($cateId)
    {
        // 获取该分类下所有启用的文章
        $articles = \App\Models\Article::where('article_cate_id', $cateId)
            ->where('status', 1)
            ->get();

        if ($articles->isEmpty()) {
            return [];
        }

        // 收集所有标签及其文章ID
        $tagData = [];
        foreach ($articles as $article) {
            foreach ($article->tags as $tag) {
                if (!isset($tagData[$tag->id])) {
                    $tagData[$tag->id] = [
                        'id' => $tag->id,
                        'name' => $tag->name,
                        'slug' => $tag->slug,
                        'sort' => $tag->sort ?? 0,
                        'article_ids' => [],
                        'count' => 0,
                    ];
                }
                $tagData[$tag->id]['article_ids'][] = $article->id;
                $tagData[$tag->id]['count']++;
            }
        }

        // 按 sort 排序
        return collect($tagData)->sortBy('sort')->values()->toArray();
    }

    /**
     * 根据页面 URI 获取标题
     */
    private function getTopicsTitleByUri($uri)
    {
        $titles = [
            'effect' => '心得分類',
            'health' => '健康分類',
            'sideeffects' => '副作用分類',
        ];

        return $titles[$uri] ?? null;
    }

    /**
     * 根据页面 URI 获取标签数据
     * 显示该分类下有文章的所有标签
     */
    private function getTopicsTagsByUri($uri)
    {
        // 根据页面 URI 获取分类
        $cate = ArticleCategoryRepository::make()->all()->where('uri', $uri)->first();
        
        if (!$cate) {
            return [];
        }

        // 获取该分类下的所有文章
        $articles = \App\Models\Article::where('article_cate_id', $cate->id)
            ->where('status', 1)
            ->orderBy('release_at', 'desc')
            ->get();

        if ($articles->isEmpty()) {
            return [];
        }

        // 提取所有文章的标签，统计每个标签的文章数量
        $tagCounts = [];
        foreach ($articles as $article) {
            foreach ($article->tags as $tag) {
                if (!isset($tagCounts[$tag->id])) {
                    $tagCounts[$tag->id] = [
                        'tag' => $tag,
                        'count' => 0,
                    ];
                }
                $tagCounts[$tag->id]['count']++;
            }
        }

        // 获取所有有文章的标签ID
        $tagIds = collect($tagCounts)->pluck('tag.id')->toArray();

        if (empty($tagIds)) {
            return [];
        }

        // 按原来的顺序获取标签信息
        $tags = \App\Models\ArticleTag::whereIn('id', $tagIds)
            ->where('status', 1)
            ->orderBy('sort', 'asc')
            ->get()
            ->map(function ($tag) use ($cate) {
                return [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'description' => $tag->description ?? '',
                    'articles' => $this->getArticlesByTag($tag->id, $cate->id),
                ];
            });

        return $tags;
    }

}
