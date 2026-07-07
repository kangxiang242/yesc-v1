<?php

namespace App\Http\Middleware;

use App\Services\ConfigService;
use Closure;

class DefendMiddleware
{
    public function handle($request, Closure $next)
    {
        $close_site = ConfigService::get('close_site');

        if (!$close_site) {
            abort(503);
        }

        $response = $next($request);
        return $response;
    }
}
