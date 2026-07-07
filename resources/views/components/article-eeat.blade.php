@props([
    'news' => null, // Article model
])

@if($news && ($news->author_name || $news->reviewer_name || $news->sources || $news->last_updated_at))

<div class="article-eeat" data-track-block="article_eeat">
    {{-- 作者資訊 --}}
    @if($news->author_name)
    <div class="eeat-item eeat-author">
        <span class="eeat-icon">✍️</span>
        <div class="eeat-body">
            <span class="eeat-label">作者：</span>
            <strong>{{ $news->author_name }}</strong>
            @if($news->author_bio)
                <span class="eeat-desc">— {{ $news->author_bio }}</span>
            @endif
        </div>
    </div>
    @endif

    {{-- 審核者資訊 --}}
    @if($news->reviewer_name)
    <div class="eeat-item eeat-reviewer">
        <span class="eeat-icon">✅</span>
        <div class="eeat-body">
            <span class="eeat-label">醫學審核：</span>
            <strong>{{ $news->reviewer_name }}</strong>
            @if($news->reviewed_at)
                <span class="eeat-date">（審核於 {{ $news->reviewed_at->format('Y-m-d') }}）</span>
            @endif
        </div>
    </div>
    @endif

    {{-- 發佈與更新時間 --}}
    <div class="eeat-item eeat-dates">
        <span class="eeat-icon">📅</span>
        <div class="eeat-body">
            <span class="eeat-label">發佈日期：</span>
            <span>{{ $news->release_at ? $news->release_at->format('Y-m-d') : '未知' }}</span>
            @if($news->last_updated_at && $news->last_updated_at->gt($news->release_at))
                <span class="eeat-updated">｜更新日期：<strong>{{ $news->last_updated_at->format('Y-m-d') }}</strong></span>
                @if($news->update_summary)
                    <span class="eeat-summary">（{{ $news->update_summary }}）</span>
                @endif
            @endif
        </div>
    </div>

    {{-- 參考來源 --}}
    @if($news->sources)
    <div class="eeat-item eeat-sources">
        <span class="eeat-icon">📚</span>
        <div class="eeat-body">
            <span class="eeat-label">參考來源：</span>
            <ul class="eeat-source-list">
                @php
                    $lines = explode("\n", trim($news->sources));
                    foreach ($lines as $line):
                        $line = trim($line);
                        if (empty($line)) continue;
                        $parts = explode('|', $line, 2);
                        $title = trim($parts[0]);
                        $url = count($parts) > 1 ? trim($parts[1]) : null;
                @endphp
                <li>
                    @if($url)
                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer">{{ $title }} ↗</a>
                    @else
                        {{ $title }}
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- 免責提示 --}}
    <div class="eeat-disclaimer">
        <p>⚠️ 本文內容僅供參考，不替代專業醫師的診斷與建議。如有健康疑問，請諮詢合格醫療人員。</p>
    </div>
</div>

@endif
