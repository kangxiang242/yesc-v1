@props([
    'source' => '', // 来源名称
    'url' => '', // 来源链接（可选）
    'text' => '', // 引用文字（可选）
])

<div class="evidence-box" data-track-block="evidence_box">
    <div class="evidence-icon">📚</div>
    <div class="evidence-content">
        @if($text)
        <div class="evidence-quote">{{ $text }}</div>
        @endif
        <div class="evidence-source">
            <span class="evidence-label">依据：</span>
            @if($url)
            <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" class="evidence-link">
                {{ $source }} ↗
            </a>
            @else
            <span class="evidence-text">{{ $source }}</span>
            @endif
        </div>
    </div>
</div>

<style>
.evidence-box {
    display: flex;
    align-items: flex-start;
    padding: 15px;
    margin: 20px 0;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 8px;
    border-left: 4px solid #3498db;
}

.evidence-icon {
    font-size: 1.5em;
    margin-right: 15px;
    opacity: 0.8;
}

.evidence-content {
    flex: 1;
}

.evidence-quote {
    font-style: italic;
    color: #555;
    margin-bottom: 10px;
    padding-left: 15px;
    border-left: 2px solid #e0e0e0;
}

.evidence-source {
    font-size: 0.95em;
}

.evidence-label {
    font-weight: 600;
    color: #2c3e50;
}

.evidence-link {
    color: #3498db;
    text-decoration: none;
    font-weight: 500;
}

.evidence-link:hover {
    text-decoration: underline;
    color: #2980b9;
}

.evidence-text {
    color: #2c3e50;
    font-weight: 500;
}

@media (max-width: 768px) {
    .evidence-box {
        padding: 10px;
        margin: 15px 0;
        flex-direction: column;
    }

    .evidence-icon {
        margin-right: 0;
        margin-bottom: 10px;
        font-size: 1.2em;
    }

    .evidence-quote {
        font-size: 0.9em;
        padding-left: 10px;
    }

    .evidence-source {
        font-size: 0.85em;
    }
}
</style>