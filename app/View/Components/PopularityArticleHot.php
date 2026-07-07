<?php

namespace App\View\Components;

use App\Models\Article;
use Illuminate\View\Component;

class PopularityArticleHot extends Component
{
    protected $cate_id;

    public function __construct($type)
    {
        $this->cate_id = $type;
    }

    public function render()
    {
        $article = Article::with('cate');
        if ($this->cate_id) {
            $article = $article->where('article_cate_id', $this->cate_id);
        }
        $article = $article->where('status', 1)->orderBy('is_recommend', 'desc')->orderBy('sort', 'desc')->limit(6)->get();

        return view('components.popularity-article-hot', compact('article'));
    }
}
