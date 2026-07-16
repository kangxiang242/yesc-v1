<?php

namespace App\Filament\Widgets;

use App\Models\AccessLog;
use Carbon\Carbon;
use Filament\Widgets\Widget;

class PageAccessRankingWidget extends Widget
{
    protected static bool $isLazy = false;

    protected static string $view = 'filament.widgets.page-access-ranking';

    protected ?string $heading = '頁面訪問排行 (前10個)';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 1;

    public ?string $filter = '7';

    protected function getFilters(): ?array
    {
        return [
            '7' => '最近7天',
            '15' => '最近15天',
            '30' => '最近1個月',
        ];
    }

    protected function getViewData(): array
    {
        $days = (int) ($this->filter ?: 7);

        $logs = AccessLog::where('method', 'GET')
            ->whereBetween('created_at', [Carbon::now()->subDays($days), Carbon::now()->endOfDay()])
            ->selectRaw('url, COUNT(id) as num')
            ->groupBy('url')
            ->orderBy('num', 'desc')
            ->limit(10)
            ->get();

        return [
            'logs' => $logs,
        ];
    }
}