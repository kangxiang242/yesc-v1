<?php

namespace App\Filament\Widgets;

use App\Models\AccessLog;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DeviceBreakdownWidget extends BaseWidget
{
    protected ?string $heading = '新設備 (最近7天)';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 1;

    protected function getColumns(): int
    {
        return 1;
    }

    protected function getStats(): array
    {
        $mobileCount = AccessLog::whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()->endOfDay()])
            ->whereIn('device', ['iphone', 'android', 'mobile'])
            ->count();

        $pcCount = AccessLog::whereBetween('created_at', [Carbon::now()->subDays(7), Carbon::now()->endOfDay()])
            ->whereNotIn('device', ['iphone', 'android', 'mobile'])
            ->count();

        return [
            Stat::make('Desktop', $pcCount)
                ->description('桌上型 / 筆電')
                ->color('primary'),
            Stat::make('Mobile', $mobileCount)
                ->description('手機 / 平板')
                ->color('success'),
        ];
    }
}