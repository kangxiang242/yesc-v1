@props([
    'news' => null, // Article model
])

@php
    // 获取关联的 FAQ
    $faqs = [];

    if ($news) {
        // 优先获取直接关联到该文章的 FAQ
        $articleFaqs = \App\Models\Faq::where('article_id', $news->id)
            ->orderBy('sort', 'desc')
            ->get();

        // 获取关联到该文章分类的 FAQ（排除已经直接关联到其他文章的）
        $cateFaqs = \App\Models\Faq::where('article_cate_id', $news->article_cate_id)
            ->whereNull('article_id')
            ->orderBy('sort', 'desc')
            ->get();

        // 合并 FAQ
        $faqs = $articleFaqs->merge($cateFaqs)->unique('id');
    }
@endphp

@if($faqs->count() > 0)

<div class="article-faq" data-track-block="article_faq">
    <h3 class="faq-title">常見問題 FAQ</h3>

    <div class="faq-list">
        @foreach($faqs as $index => $faq)
        <div class="faq-item" id="faq-{{ $faq->id }}">
            <div class="faq-question" onclick="toggleFaq({{ $faq->id }})">
                <span class="faq-icon">Q{{ $index + 1 }}</span>
                <span class="faq-text">{{ $faq->questions }}</span>
                <span class="faq-toggle-icon">▼</span>
            </div>
            <div class="faq-answer" id="faq-answer-{{ $faq->id }}" style="display: none;">
                <div class="faq-answer-content">
                    {!! $faq->answers !!}
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
function toggleFaq(id) {
    var answer = document.getElementById('faq-answer-' + id);
    var icon = document.querySelector('#faq-' + id + ' .faq-toggle-icon');

    if (answer.style.display === 'none') {
        answer.style.display = 'block';
        icon.textContent = '▲';
    } else {
        answer.style.display = 'none';
        icon.textContent = '▼';
    }
}
</script>

<style>
.article-faq {
    margin: 40px 0;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.faq-title {
    font-size: 1.5em;
    margin-bottom: 20px;
    color: #2c3e50;
    border-bottom: 2px solid #3498db;
    padding-bottom: 10px;
}

.faq-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.faq-item {
    background: white;
    border-radius: 6px;
    overflow: hidden;
    transition: box-shadow 0.3s;
}

.faq-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.faq-question {
    display: flex;
    align-items: center;
    padding: 15px;
    cursor: pointer;
    font-weight: 500;
    color: #2c3e50;
}

.faq-icon {
    background: #3498db;
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    font-weight: bold;
    margin-right: 15px;
    font-size: 0.9em;
}

.faq-text {
    flex: 1;
}

.faq-toggle-icon {
    color: #3498db;
    font-size: 0.8em;
    transition: transform 0.3s;
}

.faq-answer {
    padding: 0 15px 15px 15px;
    border-top: 1px solid #e0e0e0;
    background: #fafafa;
}

.faq-answer-content {
    padding-top: 15px;
    color: #555;
    line-height: 1.6;
}

@media (max-width: 768px) {
    .article-faq {
        padding: 15px;
        margin: 20px 0;
    }

    .faq-title {
        font-size: 1.2em;
    }

    .faq-question {
        padding: 10px;
        font-size: 0.95em;
    }

    .faq-icon {
        padding: 3px 8px;
        font-size: 0.85em;
    }
}
</style>

@endif