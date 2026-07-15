# 监听实施记录 — YESC

## 2026-07-15（下午）— Skill 更新：Release Token 前端

### 完成

- Web layout 注入 `class="{{ release_token() }}"`；jquery/iife 改 `release_asset()`
- 散落 `asset()` / `assetv()` / `?ver=asset_version` 本地 JS 统一 `release_asset()`
- `assetv()` 改为委托 `release_asset()`，去掉 filemtime `?v=`
- `release_asset()` 规范化路径并剥离旧 query
- `track.js`：仅检查 `/static/`；跳过 Vite `/build/`；拒绝 `?v=`/`?ver=` 冒充 token
- 交付 `doc/RELEASE_TOKEN.md`；更新 `doc/TRACKING_API.md` 后端待办 #8

### 后端专员优先阅读

1. [`doc/RELEASE_TOKEN.md`](RELEASE_TOKEN.md) — 版本映射、派生字段、部署 stamp
2. [`doc/TRACKING_API.md`](TRACKING_API.md) — 行为事件 + allowlist
3. [`doc/TRACKING_INVENTORY.md`](TRACKING_INVENTORY.md) — 埋点清单

---

## 2026-07-15（上午）— P0–P2 行为监听

### 完成

- 增强 `track.js`：section_dwell、双属性 DOM、session_path、UTM、插件、转化钩子
- Web 首次接入 analytics + 全站 CTA/板块
- 交付 TRACKING_API / INVENTORY

### 已知注意

1. 生命周期/板块事件有双发兼容名，报表需约定主事件
2. Vite `/build/` 不参与 release token missing 统计
3. calc 插件 N/A；外部 observer-1.0 control 通知未动
