# YesCialis v1 — 项目根文档

> 台灣犀利士 Cialis 線上訂購網站。v1 新版（Laravel 11 + Filament 3），基於 twshop-v1 模板搭建。

---

## 快速导航

| 入口 | 地址 |
|------|------|
| 🌐 前台（测试域名） | https://slir4.top |
| 🔐 后台（机密域名·线上43） | https://ami3-17drt4-6ne634russ.slir4.top/mgx7k9p2 |
| 🔐 后台（本地开发·8001） | http://localhost:8001/pthj1l0cxsau |
| 👤 后台账号 | `web0wer16888` |
| 🔑 后台密码 | `888d00rkeeper888` |
| ⚠️ 注意 | 主域名 `slir4.top/mgx7k9p2` 已 Nginx 层拦截返回 404，仅机密子域名 `ami3-17drt4-6ne634russ.slir4.top` 可访问后台（map 文件 `/etc/nginx/conf.d/admin-host-map.conf`） |

---

## 技术栈

| 组件 | 版本 |
|:-----|:-----|
| 框架 | Laravel 11.x |
| PHP | ^8.2 |
| 後台 | Filament 3.3.x |
| 数据库 | MySQL（本地 `yescialis`） |
| 缓存 | Database（prefix: `yesc_`） |
| Session | Database |
| Queue | Database |
| 前端构建 | Vite |
| Captcha | mews/captcha v3.5 |
| Excel | maatwebsite/excel v3.1 |

---

## 项目目录

```
yesc-v1/
├── app/
│   ├── Console/Commands/       # 自定義 Artisan 命令
│   ├── Events/                  # AccessEvents
│   ├── Exceptions/              # MsgException, ValidationFailedException
│   ├── Exports/                 # OrderXlsxExport
│   ├── Filament/
│   │   ├── Resources/           # 14 个后台资源(_disabled/ 内有 5 个空数据资源已停用)
│   │   ├── Pages/               # 6 个自定义页面（SiteConfig/AnalyticsReport/...）
│   │   └── Widgets/             # Dashboard widgets
│   ├── Handlers/                # DeviceTypeHandlers, ArticleAnchorsHandler
│   ├── Http/
│   │   ├── Composers/           # LayoutComposer（全局视图数据注入）
│   │   ├── Controllers/
│   │   │   ├── Web/             # 12 个前端控制器
│   │   │   ├── Admin/           # WangEditorUpload
│   │   │   └── Api/             # RegionStoreController
│   │   ├── Middleware/           # 6 个中间件
│   │   └── Requests/            # 3 个表单验证（OrderStoreRequest 等）
│   ├── Listeners/               # AccessLogListeners
│   ├── Models/                  # 27 个 Eloquent Model
│   ├── Observers/               # ArticleObserver
│   ├── Providers/               # AppServiceProvider, ViewServiceProvider, AdminPanelProvider
│   ├── Repositories/            # 9 个数据仓库（缓存层）
│   └── Services/                # 13 个业务服务
├── bootstrap/app.php            # 中间件注册（全局 + 别名）
├── config/                      # 11 个配置文件
├── database/
│   ├── migrations/              # 43 个迁移文件
│   └── seeders/                 # DatabaseSeeder + PolicyPagesSeeder 等
├── deploy/
│   └── nginx-slir7.conf         # Nginx 配置参考
├── resources/views/
│   ├── web/                     # 桌面端模板
│   ├── mobile/                  # 移动端模板
│   ├── components/              # Blade 组件视图
│   ├── partials/                # 局部片段（analytics-scripts, critical-css）
│   └── filament/                # 后台自定义视图
├── routes/web.php               # 前端路由（含区域联动 API）
└── public/storage -> ../storage/app/public  # 存储软链接
```

---

## 后端中间件

| 中间件 | 别名 | 作用范围 | 功能 |
|:-------|:----:|:--------:|:-----|
| AccessLogMiddleware | `access.log` | 全局 | 访问日志记录（跳过后台/分析端点） |
| EnforceCacheHeadersMiddleware | — | 全局 | 可缓存页面：清理 Set-Cookie + 设置 Cache-Control |
| RedirectDeviceMiddleware | `redirect.device` | 前端路由 | 移动端跳转 m 域名 |
| GooglebotChecked | `googlebot.checked` | 前端路由 | Googlebot 检测 + 投毒/伪装页面 |
| DefendMiddleware | `defend` | 可选 | 站点关闭保护（`close_site` 配置） |
| CacheableMiddleware | `cacheable.public` | 可选 | 标记页面为公共可缓存 |

---

## 路由

| 方法 | URI | 控制器 | 中间件 |
|:-----|:----|:--------|:-------|
| GET | `/` | IndexController@index | redirect.device, googlebot.checked |
| GET | `/product` | ProductController@index | 同上 |
| GET | `/goods/{id}` | ProductController@show | 同上 |
| GET | `/shopping/{id}` | OrderController@checkout | 同上 |
| POST | `/order` | OrderController@store | 同上 |
| GET | `/check` | OrderController@check | 同上 |
| POST | `/check` | OrderController@check | 同上 |
| GET | `/check/{no}` | OrderController@checking | 同上 |
| GET | `/order/{no}` | OrderController@checking | 同上 |
| GET | `/message` | MessageController@index | 同上 |
| POST | `/message` | MessageController@store | 同上 |
| GET | `/{uri}` | PageController@index | 同上 |
| GET | `/{uri}/{id}.html` | NewsController@show | 同上 |
| POST | `/observer/store` | Closure | 跳过 CSRF |
| POST | `/api/analytics/collect` | Closure | 跳过 CSRF（行为分析采集） |
| POST | `/admin/upload/wang-editor/image` | WangEditorUploadController | web |
| GET | `/area/city` | AreaController@getCity | — |
| GET | `/api/general-banner-fragment` | ApiController@generalBannerFragment | — |
| GET | `/api/regionstore/*` | RegionStoreController | — |
| GET | `/robots.txt` | ApiController@robots | — |
| GET | `/sitemap.xml` | ApiController@sitemap | — |
| GET | `/google{str}.html` | ApiController@googleVerify | — |

---

## 产品数据

首页和商品页按固定 ID 分组展示：

| 分组 | ID 范围 | 说明 |
|:-----|:--------|:-----|
| 初次體驗選擇 | 11, 12, 13 | 小盒数试用 |
| 省心推薦專區 | 14, 15, 16, 17 | 热门组合 |
| 穩定回購組合 | 18, 19, 20 | 回购买家 |
| 長期保養計畫 | 21, 22, 23, 24 | 大量购买 |

---

## 本地启动

```bash
php artisan serve --port=8001
```

> 必须使用 `localhost:8001` 访问。`127.0.0.1` 会触发 `redirect.device` 中间件重定向到配置的 APP_URL。

---

## 测试环境

| 项目 | 内容 |
|:-----|:------|
| 测试域名 | https://slir4.top |
| 测试服务器 | 5.182.210.43（原生服务，已移除 1Panel + Docker） |
| SSH 密钥 | `~/workspace/wwwroot/hk-server-keys/deploy_key`（ed25519，与 52 同密钥） |
| SSH 用户 | root@5.182.210.43:22（已禁用密码登录） |
| 站点路径 | `/opt/1panel/www/sites/slir4.top/index/` |
| Nginx 配置 | `/etc/nginx/sites-available/slir4.top` |
| PHP | 原生 PHP 8.2 FPM（`127.0.0.1:9000`） |
| Nginx | 原生 nginx 1.18.0 |
| 数据库 | 原生 MariaDB 11.4.12 `yescialis_v1`（48 张表，root / mariadb_2312） |
| 后台路径 | `/mgx7k9p2`（线上43）；本地開發用 `/pthj1l0cxsau`（ADMIN_PATH 环境变量差异） |
| 后台账号 | `web0wer16888` / `888d00rkeeper888` |
| 部署方式 | `rsync -avz --exclude={vendor,node_modules,.git,.env} -e 'ssh -i ~/workspace/wwwroot/hk-server-keys/deploy_key' ./ root@5.182.210.43:/opt/1panel/www/sites/slir4.top/index/` |
| Composer | `ssh root@5.182.210.43 'cd /opt/1panel/www/sites/slir4.top/index && composer install --no-dev'` |
| 快取清除 | `ssh root@5.182.210.43 'cd /opt/1panel/www/sites/slir4.top/index && php artisan optimize:clear'` |
| CF 账号 | `aqs33202@outlook.com` / `1ver!ter3321`（Token: ⚠️ 已从提交历史中移除，请查看 .env） |
| CF API Token | ⚠️ 已从提交历史中移除，请查看 .env |
| CF Zone ID | `97709f8bb53a452a8379fcc230c5e28e` |
| GIT 仓库 | `git@github.com:kangxiang242/yesc-v1.git`（main） |
| 后台访问限制 | 主域名 `slir4.top` 访问 `/mgx7k9p2` 或 `/admin` 一律 Nginx 404（map 文件 `/etc/nginx/conf.d/admin-host-map.conf`）；仅子域名 `ami3-17drt4-6ne634russ.slir4.top` 可进后台 |

### DNS 解析（Cloudflare）

测试域名 `slir4.top` 通过 Cloudflare DNS 解析到 5.182.210.43：

| 记录类型 | 名称 | 内容 | 代理状态 |
|:--------:|:-----|:-----|:--------:|
| A | `slir4.top` | `5.182.210.43` | 仅 DNS（或橙云代理） |

> 如需切回旧测试服务器 45.148.120.52，将上述 A 记录内容改为 `45.148.120.52` 即可。两个服务器 SSH 使用同一密钥。

### DNS 切换记录

| 日期 | 操作 | A 记录旧值 | A 记录新值 | 状态 |
|:-----|:-----|:----------:|:----------:|:----:|
| 2026-07-15 | Cloudflare DNS 切换测试服务器 | `45.148.120.52` | `5.182.210.43` | ✅ 已生效 |

- **记录 ID**：`054b44897e9632aede946fa87fe24b4c`
- **代理状态**：橙云（proxied: true）
- **生效方式**：开启橙云代理，Cloudflare 边缘节点秒级更新
- **切换 API**：`PATCH /zones/{zone_id}/dns_records/{record_id}`

---

## 已核准功能

| 功能 | 结果 |
|:-----|:----:|
| 首页渲染 | ✅ |
| 商品列表/详情 | ✅ |
| 结账下单（含便利店联动） | ✅ |
| 订单查询 | ✅ |
| 留言/客服 | ✅ |
| 文章/新闻页 | ✅ |
| 后台管理（Filament） | ✅ |
| robots.txt / sitemap.xml | ✅ |
| 移动端模板 | ✅ |
| 详见 `2-结果报告.md` | |

---

## 已修复问题

| 日期 | 文件 | 问题 | 修复 |
|:-----|:-----|:-----|:-----|
| 2026-07-09 | `OrderRepository.php:204` | 商品名称硬编码为「威而鋼」(Viagra) | 改为 `$item->name` 从数据库读取 |

## Git 记录

```bash
origin  git@github.com:kangxiang242/yesc-v1.git (fetch)
origin  git@github.com:kangxiang242/yesc-v1.git (push)

c834597  docs: 更新 root.md 为 yesc-v1（Cialis）专属内容
2fea9b7  fix: OrderRepository 商品名称硬编码错误
257b5ac  docs: 更新 Small Summer 服务器 SSH 改为密钥登录(密码已禁用)
```

---

## 变更记录

| 日期 | 内容 |
|:-----|:------|
| 2026-07-15 | 移除空数据后台板块(Anchor/Author/Exception/SiteGuide/Slide)，订单/留言管理置顶；修复 AppServiceProvider 兼容性(class_exists 保护 + error_reporting 抑制) |
| 2026-07-15 | 测试环境 DNS 解析从 45.148.120.52 切到 5.182.210.43（原生服务） |
| 2026-07-09 | 修复 OrderRepository 商品名称硬编码 |
| 2026-07-09 | root.md 全面更新为 yesc-v1 专属内容 |
| 2026-07-07 | yesc-v1 仓库建立，基于 twshop-v1 模板首次推送 |

## 参考

- 服务器文档：`/Users/a123/workspace/wwwroot/my-notes/香港集策/服务器/测试与备份/test-5.182.210.43.md`
- 旧测试服务器（备用）：45.148.120.52，文档 `/Users/a123/workspace/wwwroot/my-notes/香港集策/服务器/测试与备份/test-45.148.120.52.md`
- 原始模板：`/Users/a123/workspace/wwwroot/Y-yescialis.com/yescialis.com`
