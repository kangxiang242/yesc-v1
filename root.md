# Viagra TW Shop — 项目根文档

> 台灣威而鋼官方訂購網站。已從 Laravel 8 + Dcat Admin 遷移重建至 Laravel 11 + Filament 3。

---

## 快速导航

| 入口 | 地址 |
|------|------|
| 前台首頁 | http://localhost:8012 |
| 後台管理（本地） | http://localhost:8012/admin |
| 後台管理（新服） | https://am-d6do8-45n89inan.viagra-twshop.com |
| 後台帳號 | `web0wer16888` / `888d00rkeeper888` |
| 後台隱藏路徑 | `d262c08`（本地）/ `mgx7k9p2`（新服 ADMIN_PATH） |

---

## 技术栈

- **框架：** Laravel 11.x (PHP 8.2)
- **後台：** Filament 3.3
- **数据库：** MySQL `viagra_twshop` (root/root2312)
- **前端：** Blade 模板 (web/ + mobile/ 双端自动切换)
- **表单安全：** HMAC-SHA256 签名 + AES-256-CBC 加密 + form_token 防重复提交

---

## 本地启动

```bash
php artisan serve --port=8012
```

> 必须使用 `localhost:8012` 访问，`127.0.0.1` 会触发 `redirect.device` 中间件重定向。

---

## 项目文档

| 文档 | 路径 | 说明 |
|------|------|------|
| 项目计划 | `task_plan.md` | Dcat → Filament 迁移全计划（7个Phase） |
| E2E 测试 SOP | `sop/e2e-test-sop.md` | 端到端测试流程 |
| 人工测试 SOP | `sop/manual-test-sop.md` | 人工验收测试步骤 |
| 测试记录 | `log/` | E2E 测试截图及报告 |
| 服务器信息 | `root.md`#服务器部署 | 所有服务器部署信息（见下方） |

---

## 目录结构简览

```
app/
├── Models/              # 21 个 Eloquent Model
├── Filament/
│   ├── Resources/       # 12 个后台资源
│   └── Pages/           # 4 个自定义页面
├── Http/
│   ├── Controllers/Web/ # 13 个前端控制器
│   ├── Middleware/       # 4 个中间件
│   └── Requests/         # 表单请求验证
├── Services/             # 7 个业务服务
├── Repositories/         # 6 个数据仓库
└── View/Components/     # 4 个 Blade 组件

resources/views/
├── web/                  # 桌面端模板
├── mobile/               # 移动端模板
├── components/           # Blade 组件视图
├── partials/             # 局部片段
└── filament/             # 后台自定义页面视图

routes/web.php            # 前端路由（含区域联动 API）
config/                   # 11 个配置文件
database/migrations/      # 43 个迁移文件
public/static/            # 静态资源
public/uploads/           # 上传文件
```

---

## 伺服器資訊

### Production（生產環境 — viagra-twshop.com）

| 項目 | 內容 |
|------|------|
| 域名 | https://www.viagra-twshop.com |
| 移動端 | https://m.viagra-twshop.com |
| 後台 | /mgx7k9p2 / 帳號 `web0wer16888` / 密碼 `888d00rkeeper888` |
| IP | 5.182.210.30 |
| SSH | `ssh -i ~/.ssh/small-summer root@5.182.210.30`（密钥登录，密码已禁用） |
| 專案路徑 | `/www/sites/viagra-twshop.com` |
| Git 倉庫 | `git@github.com:kangxiang242/viagra-twshop.com-v1.git` |
| Git remote | `origin`（SSH） |
| 部署方式 | `git pull origin main` |
| PHP-FPM | `/usr/local/php82/sbin/php-fpm`（restart: `kill -USR2`） |
| 環境類型 | 原生 Ubuntu 20.04（非 Docker） |
| 資料庫 | 本機 MariaDB / `twshop_v1`（root/root2312） |
| Nginx 配置 | `/etc/nginx/sites-available/viagra-twshop.com` |
| CF 帳號 | `guangjohnwuhot1919@hotmail.com` |
| CF Zone | `493f9a41086d74ab8da6f5e523ced63d` |
| CF Token | `<redacted>` |
| CF SSL | Full |

### Test（測試環境 — slir7.top）

| 項目 | 內容 |
|------|------|
| 域名 | https://slir7.top |
| 後台 | /mgx7k9p2 / 帳號 `web0wer16888` / 密碼 `888d00rkeeper888` |
| IP | 45.148.120.52 |
| SSH | `ssh -i ~/workspace/wwwroot/hk-server-keys/deploy_key` |
| 專案路徑 | `/opt/1panel/www/sites/slir7.top/index/` |
| Git 倉庫 | `git@github.com:kangxiang242/viagra-twshop.com-v1.git` |
| Git remote | `origin`（SSH） |
| 部署方式 | `git pull --rebase origin main` |
| PHP | Docker 容器 `php82`（1Panel） |
| Nginx | Docker 容器 `1Panel-openresty-UOYX` |
| 資料庫 | 外部 5.182.210.43:3306 / `viagra_twshop` |
| CF 帳號 | `aqs33202@outlook.com` |
| CF Zone | `5e97e5827de95daaddec509940a2d450` |
| CF Token | `<redacted>` |
| CF SSL | Full |
| 備註 | Docker 1Panel 環境，配置在容器內 `/usr/local/openresty/nginx/conf/nginx.conf`（FastCGI cache key 已含 `$http_user_agent`） |

### 本地開發環境

| 項目 | 內容 |
|------|------|
| 路徑 | `/Users/a123/workspace/wwwroot/V-viagraeshop/twshop-v1` |
| Git 倉庫 | `kangxiang242/viagra-twshop.com-v1.git`（remote: `company`） |
| 啟動 | `php artisan serve --port=8012` |
| 後台路徑 | `/admin` 或 `.env` 設定的 ADMIN_PATH |
| 資料庫 | 本地 MySQL `viagra_twshop`（root/root） |

## 关键配置文件

| 文件 | 用途 |
|------|------|
| `.env` | 数据库连接、APP_KEY、表单 HMAC 密钥 |
| `config/global.php` | 全局常量配置 |
| `config/app.php` | APP_URL、M_URL、时区等 |

---

## 迁移状态

- ✅ Phase 1~3: 项目初始化、数据库/Models 迁移 — 完成
- ✅ Phase 4~5: Filament 后台、前端 Blade 模板 — 完成
- ✅ Phase 7: 测试验证 — 完成
- ✅ FileUpload 功能 — 完成
- ✅ Sitemap 生成 — 完成

---

## Git 记录

```bash
# 最新 commits
5fa7e40  docs: add 2026-07-03 daily report
2ad7cd4  fix: banner edit 500 error - handle json_decode for casted array fields
5286354  fix: 商品詳情頁 H1 改為商品名稱，移除重複標題
071d4f7  feat: 页面添加 data-dpr 设备像素比追踪
74d3a97  fix: 手機商品列表 SEO 標題與結帳 SKU 切換同步優化
23196b9  fix: .gitignore .pi/ 拼写错误
a19651b  merge: 合并 origin/main (Raingor) 到 company/main
169ed31  fix: 結帳頁地址驗證、SKU 運費同步與便利店選擇修正
```

---

## 服务器部署

### slir7.top（测试服）

| 项目 | 值 |
|------|-----|
| 域名 | https://slir7.top |
| 服务器 | 45.148.120.52（1Panel Docker） |
| SSH | `ssh -i ~/workspace/wwwroot/hk-server-keys/deploy_key root@45.148.120.52` |
| 远程路径 | `/opt/1panel/www/sites/slir7.top/index/` |
| Git 部署 | `git pull --rebase origin main`（remote: SSH `git@github.com:kangxiang242/viagra-twshop.com-v1.git`） |
| PHP | Docker 容器 `php82` → `docker exec -w /www/sites/slir7.top/index php82 php artisan ...` |
| Nginx | Docker 容器 `1Panel-openresty-UOYX` → 配置在 `/usr/local/openresty/nginx/conf/nginx.conf` |
| 数据库 | 共享 5.182.210.43:3306 / `viagra_twshop` |
| CF 代理 | ✅ 开启（橙色云），SSL Full |
| CF 账号 | `aqs33202@outlook.com`（Zone: `5e97e5827de95daaddec509940a2d450`） |
| CF Token | `<redacted>` |
| 后端 | `/mgx7k9p2` / `webower16888` / `888doorkeeper888` |
| APP_M_URL | `https://slir7.top`（同域名，触发 mobile 模板） |
| 缓存清除 | `docker exec php82 php artisan optimize:clear` + `docker exec php82 php -r 'opcache_reset();'` |
| 注意事项 | FastCGI cache key 已含 `$http_user_agent`（2026-07-03 修复） |

### viagra-twshop.com（生产服）

| 项目 | 值 |
|------|-----|
| 域名 | https://www.viagra-twshop.com |
| 移动端 | https://m.viagra-twshop.com |
| 服务器 | 5.182.210.30（原生 Ubuntu 20.04） |
| SSH | `ssh -i ~/.ssh/small-summer root@5.182.210.30`（密钥登录，密码已禁用） |
| 远程路径 | `/www/sites/viagra-twshop.com` |
| Git 部署 | `git pull --rebase origin main`（remote: SSH `git@github.com:kangxiang242/viagra-twshop.com-v1.git`） |
| PHP-FPM | `/usr/local/php82/sbin/php-fpm`（restart: `kill -USR2`） |
| Nginx 配置 | `/etc/nginx/sites-available/viagra-twshop.com` |
| 数据库 | 本机 MariaDB / `twshop_v1`（root/root2312） |
| CF 代理 | ✅ 开启，SSL Full |
| CF 账号 | `guangjohnwuhot1919@hotmail.com`（Zone: `493f9a41086d74ab8da6f5e523ced63d`） |
| CF Token | `<redacted>` |
| 管理员 | `web0wer16888` / `888d00rkeeper888` |
| APP_PATH | `mgx7k9p2` |
| APP_M_URL | `https://m.viagra-twshop.com`（移动端子域名） |
| 缓存清除 | `php artisan optimize:clear` + `php -r 'opcache_reset();'` |
| 待修复 | FastCGI cache key 未含 `$http_user_agent`（需同测试服修正） |

---

## 变更记录

| 日期 | 内容 |
|------|------|
| 2026-07-03 | 统一仓库为 `kangxiang242/viagra-twshop.com-v1.git`，移除 Raingor/twshop-v1 |
| 2026-07-03 | 测试服 FastCGI cache key 修复 → 加入 `$http_user_agent` |
| 2026-07-03 | Release Token 版本追蹤系統 Phase 1 部署 |
| 2026-07-03 | 文章保存自动清缓存（ArticleObserver + CF API） |
| 2026-07-03 | Nginx 文章页跳过缓存（`.html$` 加到 skip_cache） |
| 2026-06-13 | slir7.top CF A 记录修正为 45.148.120.52 |

---

## CF账号
| #  | 邮箱                               | 密码   | 说明         | API Token（完整版）      | API Token（Zone.DNS 版） | 域名            | 备注 |
| -- | ---------------------------------- | ------ | ------------ | ------------------------ | ------------------------ | --------------- | ---- |
| 14 | `guangjohnwuhot1919@hotmail.com` | `<redacted>` | 新系统 | `<redacted>` | `<redacted>` | `viagra-twshop.com`（Zone ID: `493f9a41086d74ab8da6f5e523ced63d`） | ⚠️ 无 Load Balancers，另有 WAF/Page Rules/Analytics |
| --- | ------------------------------------- | ----------------- | ------------- | ------------------------------------------------------- | ------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------------------------------------- |


## 参考

- 服务器文档：[test-45.148.120.52.md](/Users/a123/workspace/wwwroot/my-notes/香港集策/服务器/测试与备份/test-45.148.120.52.md)、[new-服务器.md](/Users/a123/workspace/wwwroot/my-notes/香港集策/服务器/new-服务器.md)
- 香港集策笔记：`/Users/a123/workspace/wwwroot/my-notes/香港集策/`
