# YesCialis v1 — 项目根文档

> 台灣犀利士 Cialis 線上訂購網站。v1 新版（Laravel 11 + Filament 3），基於 twshop-v1 模板搭建。

---

## 快速导航

| 入口 | 地址 |
|------|------|
| 🌐 前台（生产） | https://yescialis.com/ |
| 🔐 后台（生产·独立子域名·无后缀） | https://am-d6do8-45n89inan.yescialis.com/ |
| 🌐 前台（本地开发·8012） | http://localhost:8012/ |
| 🔐 后台（本地开发·8012·带后缀） | http://localhost:8012/pthj1l0cxsau |
| 👤 后台账号 | `web0wer16888` |
| 🔑 后台密码 | `888d00rkeeper888` |
| ⚠️ 注意 | 生产后台为独立子域名且无路径后缀；仅本地开发才允许带 `/pthj1l0cxsau` 后缀。生产服务器 IP：5.182.210.30（Small Summer） |

---

## 技术栈

| 组件 | 版本 |
|:-----|:-----|
| 框架 | Laravel 11.x |
| PHP | ^8.2 |
| 後台 | Filament 3.3.x |
| 数据库 | MySQL（生产 `yescialis_v1` / 本地 `yescialis`） |
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
php artisan serve --port=8012
```

> 必须使用 `localhost:8012` 访问。`127.0.0.1` 会触发 `redirect.device` 中间件重定向到配置的 APP_URL。本地后台需带 `/pthj1l0cxsau` 后缀；生产后台为独立子域名、无后缀。

---

## 生产环境

站点部署在 Small Summer 服务器（5.182.210.30），域名绑定原 yescialis.com 的 Cloudflare 账号。

| 项目 | 内容 |
|:-----|:------|
| 主域名（前台） | https://yescialis.com/ |
| WWW | https://www.yescialis.com/ |
| 移动端 | https://m.yescialis.com/ |
| 后台 | https://am-d6do8-45n89inan.yescialis.com/（独立子域名，无路径后缀） |
| 服务器 IP | 5.182.210.30（Small Summer） |
| SSH 密钥 | `~/.ssh/small-summer` |
| SSH 用户 | root@5.182.210.30:22 |
| 站点代码路径 | `/www/sites/yescialis.com/index` |
| Nginx 站点配置 | `/etc/nginx/sites-available/yescialis.com` |
| Nginx 可用站点 | `viagra-twshop.com` / `viagraeshop.com` / `xenicalofficial.com` / `yescialis.com` |
| PHP | 源码编译安装 `/usr/local/php82/`，FPM 服务名 `php82-fpm`（非 apt 的 `/etc/php/8.2/`） |
| Nginx | 原生 nginx |
| 数据库 | MariaDB `yescialis_v1`（root / root，127.0.0.1:3306） |
| 时区 | 全栈统一 `Asia/Taipei`（OS / PHP-FPM / Laravel / MySQL） |
| 后台账号 | `web0wer16888` / `888d00rkeeper888` |
| 部署方式 | `git pull` + `php artisan config:cache` + `php artisan route:cache` + `php artisan view:clear` + `systemctl reload php82-fpm` + `nginx -t && systemctl reload nginx` |
| GIT 仓库 | `git@github.com:kangxiang242/yesc-v1.git`（main） |
| CF 账号 | yescialis.com 属账号 #10 `christiegeorgina519532982@gmail.com` |
| CF Zone ID | `ada76e0d5948cb195cfbca2cdb99180b` |
| Cloudflare 代理 | 橙云（proxied: true），SSL 模式 Flexible（回源 80 端口） |

### DNS 解析（Cloudflare）

生产域名 `yescialis.com` 通过 Cloudflare DNS 解析到 5.182.210.30：

| 记录类型 | 名称 | 内容 | 代理状态 |
|:--------:|:-----|:-----|:--------:|
| A | `yescialis.com` | `5.182.210.30` | 橙云 ✅ |
| A | `www.yescialis.com` | `5.182.210.30` | 橙云 ✅ |
| A | `m.yescialis.com` | `5.182.210.30` | 橙云 ✅ |
| A | `am-d6do8-45n89inan.yescialis.com` | `5.182.210.30` | 橙云 ✅ |

### 已关闭单页（访问 301 → 首页）

| URI | 说明 |
|:----:|:-----|
| `/side-effects` | 副作用说明页 |
| `/contraindications` | 禁忌页 |
| `/usage` | 使用方法页 |

> 配置位置：`config/global.php` 的 `closed_pages` 数组，新增关闭页只需追加 URI 字符串。

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
| 2026-07-17 | root.md 更新：改为生产环境信息（yescialis.com / 5.182.210.30 / Small Summer）；移除"测试环境"整章（slir4.top / 5.182.210.43）；本地端口 8001→8012；补后台独立子域名无后缀、CF Zone、已关闭单页 301 等 |
| 2026-07-15 | 移除空数据后台板块(Anchor/Author/Exception/SiteGuide/Slide)，订单/留言管理置顶；修复 AppServiceProvider 兼容性(class_exists 保护 + error_reporting 抑制) |
| 2026-07-15 | 测试环境 DNS 解析从 45.148.120.52 切到 5.182.210.43（原生服务） |
| 2026-07-09 | 修复 OrderRepository 商品名称硬编码 |
| 2026-07-09 | root.md 全面更新为 yesc-v1 专属内容 |
| 2026-07-07 | yesc-v1 仓库建立，基于 twshop-v1 模板首次推送 |

## 参考

- 生产服务器文档：`/Users/a123/workspace/wwwroot/my-notes/香港集策/服务器/new-服务器.md`
- 旧测试服务器（备用）：45.148.120.52，文档 `/Users/a123/workspace/wwwroot/my-notes/香港集策/服务器/测试与备份/test-45.148.120.52.md`
- 原始模板：`/Users/a123/workspace/wwwroot/Y-yescialis.com/yescialis.com`
