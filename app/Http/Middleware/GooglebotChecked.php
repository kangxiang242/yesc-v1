<?php

namespace App\Http\Middleware;

use App\Handlers\DeviceTypeHandlers;
use App\Services\ConfigService;
use Closure;

class GooglebotChecked
{
    public function handle($request, Closure $next)
    {
        if ($request->path() == '/') {
            $user_agent = $request->userAgent();
            if ($user_agent && strpos(strtolower($user_agent), 'googlebot') !== false) {
                $close_googlebot = ConfigService::get('close_googlebot');
                $host_addr = gethostbyaddr($request->header('cf-connecting-ip', $request->ip()));
                if ($host_addr && strpos(strtolower($host_addr), 'googlebot') !== false) {
                    if ($close_googlebot) {
                        return response('', 500);
                    } else {
                        $is_mobile = DeviceTypeHandlers::isMobile();
                        if ($is_mobile) {
                            $googlebot_index_page = ConfigService::get('googlebot_index_page_m');
                        } else {
                            $googlebot_index_page = ConfigService::get('googlebot_index_page');
                        }

                        if ($googlebot_index_page) {
                            echo $googlebot_index_page;
                            exit;
                        }
                    }
                }
            }
        }

        return $next($request);
    }
}
