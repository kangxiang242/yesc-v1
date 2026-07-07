<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class EnvironmentInfoWidget extends Widget
{
    protected static string $view = 'filament.widgets.environment-info';

    protected int | string | array $columnSpan = 'full';

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
