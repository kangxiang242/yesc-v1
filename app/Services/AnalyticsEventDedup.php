<?php

namespace App\Services;

/**
 * 双发事件去重服务（doc/TRACKING_API.md #7）
 *
 * 前端兼容性双发事件对：
 *   page_leave ↔ page_exit
 *   block_view ↔ section_view
 *   scroll_milestone ↔ scroll_depth
 *
 * 规范化为单一主事件，避免报表重复计数。
 */
class AnalyticsEventDedup
{
    /**
     * 双发事件对：[alias => canonical]
     */
    private const EVENT_ALIASES = [
        'page_exit'        => 'page_leave',
        'section_view'     => 'block_view',
        'scroll_depth'     => 'scroll_milestone',
    ];

    /**
     * 规范化 event_name（alias → canonical）
     *
     * @param string $event_name
     * @return string
     */
    public function canonicalize(string $event_name): string
    {
        return self::EVENT_ALIASES[$event_name] ?? $event_name;
    }

    /**
     * 判断是否为双发事件中的 alias（次要事件）
     *
     * @param string $event_name
     * @return bool
     */
    public function isAlias(string $event_name): bool
    {
        return isset(self::EVENT_ALIASES[$event_name]);
    }
}
