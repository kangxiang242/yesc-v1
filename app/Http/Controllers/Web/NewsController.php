<?php

namespace App\Http\Controllers\Web;


use App\Http\Controllers\Controller;
use App\Repositories\ArticleCategoryRepository;
use App\Repositories\ArticleRepository;
use App\Services\ArticleService;

class NewsController extends Controller
{
    private $articleRepository;
    private $articleService;

    public function __construct(
        ArticleRepository $articleRepository,
        ArticleService $articleService
    ) {
        $this->articleRepository = $articleRepository;
        $this->articleService = $articleService;
    }

    public function show($uri, $id)
    {
        $cate = ArticleCategoryRepository::make()
            ->all()
            ->where('uri', $uri)
            ->first();

        if (!$cate) {
            abort(404);
        }

        $news = $this->articleRepository
            ->all()
            ->where('article_cate_id', $cate->id)
            ->where('id', intval($id))
            ->first();

        if (!$news) {
            abort(404);
        }

        $next = $this->articleRepository->getNext(intval($id), $cate->id);
        $prev = $this->articleRepository->getPrev(intval($id), $cate->id);
        $newNews = $this->articleRepository->newNews(3);
        $top = $this->articleRepository->top(3);

        $parsed = $this->articleService
            ->parseContentWithToc($news->content);

        $content = $parsed['content'];
        $toc = $parsed['toc'];
        $firstParagraph = $parsed['first_paragraph'] ?? null;

        return template(
            'news.show',
            compact(
                'news',
                'content',
                'toc',
                'firstParagraph',
                'next',
                'prev',
                'top',
                'newNews'
            )
        );
    }

    public function show2($uri, $id)
    {
        return redirect()->route('news.show', [$uri, $id], 301);
    }
}
