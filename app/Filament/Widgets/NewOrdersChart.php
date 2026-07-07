<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NewOrdersChart extends BaseWidget
{
    protected ?string $heading = '新訂單';

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

    protected function getStats(): array
    {
        $option = $this->filter;

        switch ($option) {
            case '30':
                $start = Carbon::now()->subDays(30);
                $end = Carbon::now()->endOfDay();
                break;
            case '15':
                $start = Carbon::now()->subDays(15);
                $end = Carbon::now()->endOfDay();
                break;
            case '7':
                $start = Carbon::now()->subDays(7);
                $end = Carbon::now()->endOfDay();
                break;
            case '-1':
                $start = Carbon::now()->subDay()->startOfDay();
                $end = Carbon::now()->subDay()->endOfDay();
                break;
            default: // '0' => today
                $start = Carbon::now()->startOfDay();
                $end = Carbon::now()->endOfDay();
        }

        $total = Order::whereBetween('created_at', [$start, $end])->count();

        $label = match ($option) {
            '30' => '最近30天',
            '15' => '最近15天',
            '7' => '最近7天',
            '-1' => '昨日',
            default => '今日',
        };

        return [
            Stat::make($label, $total)
                ->description('新訂單數量')
                ->color('primary'),
        ];
    }
}