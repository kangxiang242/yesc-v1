# Tracking API — 前端对接说明（YESC / 犀利士）

> 前端已完成 P0–P2 埋点。本文档供后端专员扩展采集、校验与报表。**勿引入第二套 endpoint。**

## Endpoint（唯一）

| 项 | 值 |
|----|-----|
| URL | `POST /api/analytics/collect` |
| CSRF | 已豁免（`withoutMiddleware(VerifyCsrfToken)`） |
| Content-Type | `application/json` |
| 发送方式 | 批量 JSON；关页/跳转优先 `navigator.sendBeacon` + `Blob` |

另有历史占位 `POST/ANY /observer/store`（订单/留言旧通知），**行为分析不走此路径**。

## 请求体结构

```json
{
  "platform": "web|mobile",
  "visitor_id": "uuid",
  "session_id": "uuid",
  "page_view_id": "uuid",
  "events": [
    {
      "event_name": "page_view",
      "client_ts": 1710000000000,
      "page_path": "/",
      "page_type": "home",
      "page_title": "...",
      "props": { }
    }
  ]
}
```

当前 `routes/web.php` 已写入 `analytics_events`（`App\Models\AnalyticsEvent`）。请确认报表/聚合覆盖下方**新事件名**。

## 顶层 / 事件字段

| 字段 | 位置 | 说明 |
|------|------|------|
| platform | body | web / mobile |
| visitor_id | body | Cookie 持久 UUID |
| session_id | body | Cookie，30min 无活动续期 |
| page_view_id | body | 每页浏览 UUID |
| event_name | event | 见事件表 |
| page_path / page_type / page_title | event | |
| props.* | event.props | 事件元数据（见 allowlist） |

### 常见 props

| 字段 | 说明 |
|------|------|
| goods_id / product_id / article_id | 业务 ID |
| utm_source / utm_medium / utm_campaign | Cookie `_yesc_utm`（URL 首次捕获） |
| session_path | 本 session 路径数组 |
| blocks_seen / last_block_id / last_section_id | 板块旅程 |
| duration_before_click_sec / max_scroll_before_click_percent | click 上下文 |
| section_label / duration_sec / visibility_ratio_peak | section_dwell |
| engagement_type | bounce / quick_navigate / skim / read / deep_read |
| explain / label | 中文运营文案（来自 `data-observer`） |
| html_token / asset_* / dpr | 发布一致性诊断（既有） |

## 事件类型（event_name）

### 生命周期

| event_name | 触发 | 关键 props |
|------------|------|-----------|
| page_view | 进页 | utm_*、session_path |
| page_leave / page_exit | 关页/跳转（同内容双发兼容） | duration_sec、engagement_type、checkout_outcome… |
| scroll_milestone / scroll_depth | 25/50/75/100 | percent、scroll_target |

### 板块

| event_name | 触发 | 关键 props |
|------------|------|-----------|
| block_view / section_view | 首次入视口 ≥35% | block_id、section_label |
| section_dwell | 离开视口或 pagehide | duration_sec、visibility_ratio_peak |

DOM：`data-track-block`（mobile 既有）与 `data-track-section-view` + `data-track-section`（web skill 规范）均支持。

### 交互

| event_name | 触发 |
|------------|------|
| click | `data-track` / `data-observer` / `data-track-name` |
| field_interact | 表单 focus/blur/change；仅 field/action/filled；order_type 可带 value 0/1 |
| validation_error | FormHelper 校验失败 |
| begin_checkout | checkout 页进页 |
| order_submit / purchase | 下单成功（有 order_no 时另发 purchase） |
| order_submit_error / submit_fail | 下单失败 |
| message_submit / message_submit_error | 留言 |
| order_check_submit / order_check_success | 查单 |
| area_load | 县市/路段/门市 AJAX |
| delivery_type_change / cascade_step | 宅配/超商切换 |

### 内容 / P2

| event_name | 页面 |
|------------|------|
| content_enter / read_progress / content_abandon | news_detail、cms |
| toc_nav / toc_expand | 文章目录 |
| hero_slide_view | 首页轮播 |
| faq_toggle | FAQ |
| sticky_buy_view / sticky_buy_click | 产品 sticky |

## Release Token（版本追踪）

前端在**每个事件**的 props 中附带（主要看 `page_view`）：

| 字段 | 说明 |
|------|------|
| html_token | `<html class>` 中的 12 位 release token |
| asset_tokens | 本页 `/static/` CSS/JS URL 中提取的 token 去重列表 |
| asset_missing_token_count | 缺少合法 `?token` 的 `/static/` 资源数 |
| asset_count | 参与检查的 `/static/` CSS/JS 数 |
| asset_mismatch_samples | 与 html_token 不一致的 URL 样本（最多 3） |

Vite `/build/` 资源**不计入**上述统计（文件名哈希）。

完整 release 方案与后端派生字段见 **[`doc/RELEASE_TOKEN.md`](RELEASE_TOKEN.md)**。

## metadata / props 隐私 allowlist

**允许：** field, action, product_id, goods_id, href, element_id, error_code, depth_percent, milestone, percent, duration_sec, duration_seconds, visibility_ratio_peak, section_label, order_no, slide_index, faq_id, expanded, engagement_type, blocks_seen, last_section_id, last_block_id, duration_before_click_sec, max_scroll_before_click_percent, checkout_duration_sec, checkout_outcome, last_field, fields_touched, submit_clicked, status, step, filled, changed, value（仅 order_type）, utm_*, session_path, scroll_target, explain, label, banner_id, click_zone, target_path, next_uri, article_id, cms_uri, category_uri, heading_id, max_read_progress, html_token, asset_tokens, asset_missing_token_count, asset_count, asset_mismatch_samples, dpr

**禁止入库：** 姓名、电话、邮箱、地址、留言正文、县市/路段选中**文本值**。

## 调试

页面加 `?track_debug=1` → console 打印 `[Track] event_name payload`。

## 前端文件

```
public/static/js/track.js
public/static/js/track.min.js          # 与 track.js 同步副本（兼容旧引用）
public/static/js/tracker-plugins/
  checkout.js | home.js | reading.js | product.js
resources/views/partials/analytics-scripts.blade.php
app/helpers.php                        # release_token / release_asset / assetv
```

Layout：`web/layout.blade.php`、`mobile/layout.blade.php` 均注入 SDK + html release token。

## 后端待办清单

1. 确认 `AnalyticsEvent` 表能存新 `event_name`（通常 JSON props 即可）
2. props / metadata allowlist 校验 + PII 丢弃
3. IP 限流（建议 120/min）
4. 报表维度：platform / page_type / event_name / section(block_id) / engagement_type
5. 订单表关联 `visitor_id`（Cookie `vid_web` / `vid_m`）
6. 可选：UTM 跨域 302 保留 middleware
7. 兼容：`page_leave` 与 `page_exit`、`block_view` 与 `section_view`、`scroll_milestone` 与 `scroll_depth` 为双发，报表去重或取其一
8. **Release Token（详见 doc/RELEASE_TOKEN.md）**：按 html_token 映射 `release_version` / `release_deployed_at`；派生 `release_status`、`asset_token_status`；部署强制 `release:stamp`；废弃 SiteConfig `asset_version`

## 与旧 observer 的关系

- `observer-1.0.js`：订单/留言成功后 POST 外部 control 域名，**与 analytics 无关**，勿合并。
- 行为分析只认 `/api/analytics/collect`。
