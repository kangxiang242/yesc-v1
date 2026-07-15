# Release Token 版本追蹤 — YESC（犀利士 / yesc-v1）

> 前端已落地。本文供**后端专员**完成 stamp 流程治理、映射查询与报表。  
> Skill 模板来源：`laravel-site-tracking/templates/RELEASE_TOKEN.md`

## 目的

追踪「使用者实际拿到的前端版本」，不是服务器当前最新版。  
旧 HTML 被快取时，前端应回报旧 token；新 HTML 回报新 token。

## 本站现状（前端已完成）

| 项 | 状态 | 说明 |
|----|------|------|
| `release_token()` / `release_asset()` | ✅ | `app/helpers.php` |
| `php artisan release:stamp` | ✅ | `App\Console\Commands\ReleaseStamp` |
| `releases` 表 / `Release` model | ✅ | 保留历史记录 |
| Mobile `<html class="{{ release_token() }}">` | ✅ | `mobile/layout.blade.php` |
| Web `<html class="{{ release_token() }}">` | ✅ | `web/layout.blade.php`（本次补齐）|
| 本地 CSS/JS 走 `release_asset()` | ✅ | PC/M layout + 页面级脚本；`assetv()` 已改为委托 `release_asset()` |
| `page_view` 回报 token 摘要 | ✅ | `track.js` → `/api/analytics/collect` props |
| collect 写入 `release_token` 列 | ⚠️ 部分 | 已从 `props.html_token` 写入；**未派生** version/status |

Token 规则：`^[a-z][a-z0-9]{11}$`（12 位，首位字母）。URL 形态：`/static/.../file.js?k8f3xq9z2m1p`（**禁止** `?v=` / `?ver=` / `rt=`）。

## page_view props（前端已发）

```json
{
  "html_token": "k8f3xq9z2m1p",
  "asset_tokens": ["k8f3xq9z2m1p"],
  "asset_missing_token_count": 0,
  "asset_count": 8,
  "asset_mismatch_samples": []
}
```

采集规则（前端）：

- `html_token`：从 `<html class>` 用正则识别（兼容 Lenis 等多 class）
- 仅扫描同源 `/static/` 下 CSS/JS；**跳过** Vite `/build/`（文件名自带 hash）
- query 为纯 token 才计入；`?v=` / `?ver=` 计为 missing

## 本站本地资产清单（`release_asset` 覆盖）

### Web layout

- `static/mobile/js/jquery.min.js`
- `static/mobile/js/iife.min.js`
- `static/js/track.js`（via `partials/analytics-scripts`）
- Vite：`resources/scss/*.scss` + `resources/js/app.js` → `/build/*`（**不**挂 release token，由 Vite hash 管理）

### Web 页面级

- `static/js/FormHelper.js` / `sweetalert2.js` / `price-animator.js` 等

### Mobile layout（既有）

- bootstrap / styles / common / global / iconfont / swiper CSS
- jquery / rem / menu / layer / iife / swiper JS
- 页面级：product / checkout / article / index CSS/JS 等

### 刻意不做（第一阶段）

- 图片 / 视频 / 字体 URL
- 错误页 503（可选）
- Filament 后台 wangEditor 资源
- 第三方 CDN

## 后端待办（专员）

### 1. Release 映射增强

收到 `page_view` 后派生并落库（建议加列或 JSON 派生表）：

| 派生字段 | 逻辑 |
|----------|------|
| `release_version` | `releases.token = html_token` → version |
| `release_deployed_at` | 同上 → deployed_at |
| `release_status` | `known` / `unknown` / `missing` / `invalid` |
| `asset_token_status` | 见下 |

`release_status`：

- `missing`：无 html_token
- `invalid`：不符合 `^[a-z][a-z0-9]{11}$`
- `known`：manifest 查到
- `unknown`：格式对但查无历史

`asset_token_status`：

- `ok`：`asset_missing_token_count=0` 且 `asset_tokens` 全等于 `html_token`
- `missing`：`asset_missing_token_count > 0`
- `mismatch`：存在 ≠ html_token 的 asset token（看 `asset_mismatch_samples`）
- `not_reported`：props 无这些字段

### 2. 部署强制 stamp

正式发版流程加入：

```bash
php artisan release:stamp
# 或
php artisan release:stamp --bump=patch
php artisan release:stamp --version=1.05
```

之后清理 view/config cache（command 内已 `Cache::flush()`，生产请确认 `view:clear` / `config:clear` 是否还需显式执行）。

### 3. 报表

- 按 `release_version` / `release_token` 聚合 PV、转化、bounce
- 监控 `asset_token_status != ok` 比例（新 HTML 配旧 CSS 信号）
- 历史 token 必须可查，**不要**只保留最新 release

### 4. 订单关联（可选加强）

- `orders.release_token` 已有列；确认 checkout 成功时写入当前 `release_token()` 或客户端回报 token
- 与 analytics `visitor_id` + `release_token` 可对账「哪版前端产生订单」

### 5. 限流与隐私

- collect IP 限流建议 120/min
- props allowlist 须包含：`html_token`, `asset_tokens`, `asset_missing_token_count`, `asset_count`, `asset_mismatch_samples`

### 6. SiteConfig `asset_version`

后台仍有 `asset_version` 配置项。前端已不再依赖 `?ver=asset_version`。  
**建议后端**：标记废弃或从 SiteConfig 移除，避免运维继续改它却无效果。

## 验收清单

1. 源码：`<html class="xxxxxxxxxxxx">`（12 位）出现于 PC/M
2. Network：本地 `/static/...css|js?xxxxxxxxxxxx`
3. `page_view` props 含 `html_token` / `asset_tokens`
4. `release:stamp` 后新页面输出新 token；旧 token 仍可从 `releases` 表查出 version
5. 模拟去掉某个 `release_asset` → `asset_missing_token_count >= 1`

## 前端文件索引

```
app/helpers.php                          # release_token / release_asset / assetv→release_asset
app/Console/Commands/ReleaseStamp.php
app/Models/Release.php
public/static/js/track.js                # 采集 html/asset token（跳过 /build）
resources/views/web/layout.blade.php
resources/views/mobile/layout.blade.php
resources/views/partials/analytics-scripts.blade.php
```

行为埋点全集见 `doc/TRACKING_API.md`。
