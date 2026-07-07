@props([
    'brief' => null,
])

@if($brief)

<div class="answer-first" data-track-block="article_answer_first">
    <div class="answer-first-content">
        <div class="answer-first-icon">💡</div>
        <div class="answer-first-text">{{ $brief }}</div>
    </div>
    <div class="answer-first-disclaimer">
        ⚠️ 本文內容僅供參考，不替代專業醫師建議。使用藥品前請諮詢合格醫療人員。
    </div>
</div>

@endif
