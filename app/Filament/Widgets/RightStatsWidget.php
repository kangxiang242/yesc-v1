<?php

namespace App\Filament\Widgets;

use App\Models\AccessLog;
use App\Models\Message;
use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RightStatsWidget extends BaseWidget
{
    protected static bool $lazy = false;

    protected ?string $heading = '數據概覽';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 1;

    public ?string $filter = '0';

    protected function getFilters(): ?array
    {
        return [
            '0' => '今日',
            '-1' => '昨日',
            '7' => '最近7天',
            '15' => '最近15天',
            '30' => '最近30天',
        ];
    }

    protected function getColumns(): int
    {
        return 1;
    }

    protected function getStats(): array
    {
        $option = $this->filter;

        switch ($option) {
            case '30':
                $start = Carbon::now()->subDays(30);
                break;
            case '15':
                $start = Carbon::now()->subDays(15);
                break;
            case '7':
                $start = Carbon::now()->subDays(7);
                break;
            case '-1':
                $start = Carbon::now()->subDay()->startOfDay();
                break;
            default:
                $start = Carbon::now()->startOfDay();
        }

        $end = match ($option) {
            '-1' => Carbon::now()->subDay()->endOfDay(),
            default => Carbon::now()->endOfDay(),
        };

        $orderCount = Order::whereBetween('created_at', [$start, $end])->count();
        $msgCount = Message::whereBetween('created_at', [$start, $end])->count();

        $mobileCount = AccessLog::whereBetween('created_at', [$start, $end])
            ->whereIn('device', ['iphone', 'android', 'mobile'])
            ->count();
        $pcCount = AccessLog::whereBetween('created_at', [$start, $end])
            ->whereNotIn('device', ['iphone', 'android', 'mobile'])
            ->count();

        return [
            Stat::make('新訂單', $orderCount)
                ->description('訂單數量')
                ->color('primary'),
            Stat::make('新留言', $msgCount)
                ->description('留言數量')
                ->color('warning'),
            Stat::make('新設備', "PC {$pcCount} / Mobile {$mobileCount}")
                ->description('最近7天')
                ->color('success'),
        ];
    }
}