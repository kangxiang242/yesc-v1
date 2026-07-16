<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class DashboardLeftPanel extends Widget
{
    protected static bool $isLazy = false;

    protected static string $view = 'filament.widgets.dashboard-left-panel';

    protected int | string | array $columnSpan = 1;

    protected static ?int $sort = 0;

    protected function getViewData(): array
    {
        return [
            'phpVersion' => PHP_VERSION,
            'laravelVersion' => app()->version(),
            'cacheDriver' => config('cache.default'),
            'sessionDriver' => config('session.driver'),
            'queueDriver' => config('queue.default'),
            'timezone' => config('app.timezone'),
            'locale' => config('app.locale'),
            'env' => app()->environment(),
            'appUrl' => config('app.url'),
        ];
    }
}