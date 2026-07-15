# 站点监听清单 — YESC（犀利士 / yesc-v1）

> Phase 0 侦察 + 前端实施结果。后端对接见 `doc/TRACKING_API.md`。

## 1. 基础设施

| 项 | 值 |
|----|-----|
| endpoint | `POST /api/analytics/collect` |
| CSRF 豁免 | 是（collect + observer/store） |
| layout | `web/layout.blade.php`、`mobile/layout.blade.php` |
| SDK | `public/static/js/track.js` + `tracker-plugins/*` |
| 表单 | FormHelper（web checkout/message/check）+ api.js（mobile） |
| 既有表 | `analytics_events` / `AnalyticsEvent` |

## 2. 路由 → page_type

| 路由 | page_type | 额外字段 | 插件 |
|------|-----------|----------|------|
| `/` | home | — | home |
| `/product` | product_list | — | — |
| `/goods/{id}` | product_detail | goods_id | product |
| `/shopping/{id}` | checkout | goods_id | checkout |
| `/message` | message | — | — |
| `/check` | order_check | — | — |
| `/order/{no}`、`/check/{no}` | order_show | — | — |
| `{uri}`（CMS） | cms | cms_uri | reading |
| `{uri}/{id}` 新闻 | news_detail | article_id, category_uri | reading |
| `{uri}` 列表类新闻 | news_list | category_uri | — |
| verify | order_verify | — | — |
| 404 | not_found | — | — |

## 3. 板块清单（section_view + section_dwell）

### Web

| section id | DOM | section_label | 页面 |
|------------|-----|---------------|------|
| home.hero | `.index-banner` | 首屏 Banner | home |
| home.compare | `#compare` | 效果對比 | home |
| home.products | `.product.watermark` | 首页产品列表 | home |
| home.intro / usage / work / safety / health | 对应 section | … | home |
| product.list.{group} | `.box-container` | 分组标题 | product_list |
| product.detail.main | `.goods-info-card` | 商品主資訊 | product_detail |
| product.sticky | `.footer-buy` | Sticky購買條 | product_detail |
| checkout.form | `#order-form` | 結帳表單 | checkout |
| news.content | `#articleContent` | 文章正文 | news_detail |
| cms.content | `#spageContent` | CMS正文 | cms |
| footer.brand / footer.nav | footer | 頁腳 | 全站 |

### Mobile（既有 data-track-block，已增强 dwell）

| section id 示例 | 说明 |
|-----------------|------|
| m_home_* / m_pd_* / m_co_* / m_nd_* / m_layout_* | 保持原 ID，新增 section_dwell |

## 4. CTA 清单（click）

| 页面 | 描述 | data-observer | data-track-name |
|------|------|---------------|-----------------|
| header | 线上订购 | 頂部-線上訂購 | header.order_btn |
| header | Logo / 导航 | 頂部-* | header.* |
| mobile_nav | 侧栏项 | 側欄-* | mobile_nav.* |
| footer | 导航 | 頁腳-* | footer.nav.* |
| home hero | CTA | 首屏-* | home.hero.cta |
| home products | 立即订购 | 首頁-立即訂購-* | home.product.checkout |
| product_list | 查看详情 | 列表-* | product.list.* |
| product_detail | 立即订购 / sticky | 詳情-*/Sticky-* | product.buy / product.sticky.buy |
| checkout | 提交订单 | 結帳-提交訂單 | checkout.submit |
| message | 送出 | 留言-確認送出 | message.submit |
| nav | 开/关侧栏 | 側欄-打開/關閉 | nav.menu.open/close |

Mobile 仍用 `m_*` 前缀 `data-track`，兼容保留。

## 5. 站点特有能力

- [x] 7-11 / 宅配级联 → `area_load`（xarea.js）
- [x] 移动 drawer → `nav.menu.open/close`
- [x] Hero 轮播 → `hero_slide_view`（MutationObserver）
- [x] 文章 TOC → `toc_*`
- [x] Sticky 购买条 → `sticky_buy_*`
- [x] Release Token → 见 `doc/RELEASE_TOKEN.md`
- [ ] BMI/计算器 → **N/A**

## 6. 不做 / N/A

| 能力 | 原因 |
|------|------|
| calc 插件 | 本站无 BMI/BMR 计算器页 |
| Xenical 单条 form-urlencoded `/observer/store` 行为流 | 已有 JSON batch collect；避免双写 |
| 改 PHP Migration / 派生字段落库 | 用户要求前端 only；见 RELEASE_TOKEN 后端待办 |
| Vite `/build` 挂 release token | Vite 文件名自带 hash；track.js 跳过统计 |

## 7. 验收提示

1. 打开首页 + `?track_debug=1`，Network 滤 `collect`
2. 应见 `page_view` → 滚动 `section_view`/`section_dwell` → click → `page_exit`
3. 进 `/shopping/{id}` 应见 `begin_checkout` + `field_interact`（聚焦 phone 无号码）
