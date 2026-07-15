<?php

namespace App\Services;

/**
 * 行为分析 props allowlist 校验 + PII 丢弃服务（doc/TRACKING_API.md）
 *
 * allowlist 字段会被保留；其余字段会被丢弃。
 * PII 字段（姓名/电话/邮箱/地址/留言正文/县市路段文本）禁止入库，强制丢弃。
 */
class AnalyticsPropsFilter
{
    /**
     * props allowlist（仅这些字段会被保留）
     */
    private const ALLOWED_KEYS = [
        // 通用
        'field', 'action', 'product_id', 'goods_id', 'href', 'element_id',
        'error_code', 'depth_percent', 'milestone', 'percent', 'duration_sec',
        'duration_seconds', 'visibility_ratio_peak', 'section_label', 'order_no',
        'slide_index', 'faq_id', 'expanded', 'engagement_type', 'blocks_seen',
        'last_section_id', 'last_block_id', 'duration_before_click_sec',
        'max_scroll_before_click_percent', 'checkout_duration_sec',
        'checkout_outcome', 'last_field', 'fields_touched', 'submit_clicked',
        'status', 'step', 'filled', 'changed',
        // value 仅 order_type 允许（前端约定）
        'value',
        // utm / session
        'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content',
        'session_path',
        // scroll
        'scroll_target',
        // 运营文案
        'explain', 'label',
        // banner / click
        'banner_id', 'click_zone', 'target_path', 'next_uri',
        // 文章
        'article_id', 'cms_uri', 'category_uri', 'heading_id', 'max_read_progress',
        // release token
        'html_token', 'asset_tokens', 'asset_missing_token_count',
        'asset_count', 'asset_mismatch_samples', 'dpr',
    ];

    /**
     * 过滤 props，保留 allowlist 字段，丢弃其余（含 PII）
     *
     * @param array|null $props
     * @return array|null
     */
    public function filter(?array $props): ?array
    {
        if (!is_array($props) || empty($props)) {
            return $props;
        }

        $filtered = [];
        foreach ($props as $key => $value) {
            if (in_array($key, self::ALLOWED_KEYS, true)) {
                $filtered[$key] = $value;
            }
            // 其余字段直接丢弃（包括 PII：姓名/电话/邮箱/地址/留言正文/县市路段文本）
        }

        return $filtered;
    }
}
