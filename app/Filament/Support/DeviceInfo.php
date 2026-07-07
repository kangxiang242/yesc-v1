<?php

namespace App\Filament\Support;

use App\Handlers\DeviceTypeHandlers;

/**
 * 终端/浏览器识别辅助。
 * 注意：原任务参考的 DeviceTypeHandlers::getBrowser() 在 yescialis 中并不存在
 * （app/Handlers 不允许修改），这里用轻量解析补齐「瀏覽器」一行。
 */
class DeviceInfo
{
    public static function device(?string $ua): string
    {
        return DeviceTypeHandlers::getDevice($ua);
    }

    public static function browser(?string $ua): string
    {
        if (! $ua) {
            return '未知';
        }

        $ua = strtolower($ua);

        $map = [
            'edg/' => 'Edge', 'edg' => 'Edge', 'opr/' => 'Opera', 'opera' => 'Opera',
            'chrome' => 'Chrome', 'crios' => 'Chrome',
            'firefox' => 'Firefox', 'fxios' => 'Firefox',
            'safari' => 'Safari',
            'msie' => 'IE', 'trident' => 'IE',
            'postmanruntime' => 'Postman', 'curl' => 'Curl',
            'python' => 'Python', 'java' => 'Java', 'okhttp' => 'OkHttp',
        ];

        foreach ($map as $key => $name) {
            if (strpos($ua, $key) !== false) {
                return $name;
            }
        }

        return '未知';
    }
}
