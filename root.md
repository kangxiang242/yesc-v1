# YesCialis — 项目根文档

> 台灣犀利士 Cialis 官方訂購網站。基於 twshop-v1 模板搭建的 Laravel 11 + Filament 3 v1 版本。

---

## 快速导航

| 入口 | 地址 |
|------|------|
| 前台首頁 | http://localhost:8001 |
| 後台管理（本地） | http://localhost:8001/pthj1l0cxsau |
| 後台帳號 | `web0wer16888` / `888d00rkeeper888` |
| 後台隱藏路徑 | `pthj1l0cxsau`（ADMIN_PATH） |

---

## 技术栈

- **框架：** Laravel 11.x (PHP 8.2)
- **後台：** Filament 3.3
- **数据库：** MySQL `yescialis` (root/root2312)
- **前端：** Blade 模板 (web/ + mobile/ 双端自动切换)
- **前端构建：** Vite

---

## 本地启动

```bash
php artisan serve --port=8001
```

> 必须使用 `localhost:8001` 访问，`127.0.0.1` 会触发 `redirect.device` 中间件重定向。

---

## 项目文档

| 文档 | 路径 | 说明 |
|------|------|------|
| 今日任务 | `../../C-Company/T-Task/2026-07月/第2周/2026-07-09-周四/01-Cialis测试域名功能完善bug修复/1-Cialis测试域名功能完善bug修复.md` | Cialis 测试域名功能完善 |
| 核准报告 | `../../C-Company/T-Task/2026-07月/第2周/2026-07-09-周四/01-Cialis测试域名功能完善bug修复/2-结果报告.md` | 功能核准结果与问题清单 |

---

## 目录结构简览

```
app/
├── Models/              # 27 个 Eloquent Model
├── Filament/
│   ├── Resources/       # 19 个后台资源
│   └── Pages/           # 6 个自定义页面
├── Http/
│   ├── Controllers/Web/ # 13 个前端控制器
│   ├── Middleware/       # 6 个中间件
│   └── Requests/         # 3 个表单请求验证
├── Services/             # 13 个业务服务
├── Repositories/         # 9 个数据仓库
└── View/Components/     # Blade 组件

resources/views/
├── web/                  # 桌面端模板（75 个视图文件）
├── mobile/               # 移动端模板
├── components/           # Blade 组件视图
├── partials/             # 局部片段
└── filament/             # 后台自定义页面视图

routes/web.php            # 前端路由（含区域联动 API）
config/                   # 11 个配置文件
database/migrations/      # 43 个迁移文件
```

---

## 伺服器資訊

### Production（生產環境 — yescialis.com / cialiseshop.com）

| 項目 | 內容 |
|------|------|
| 域名 | https://www.yescialis.com |
| 伺服器 | 45.148.120.210（#210）/ 111.90.151.121（#121 備援） |
| 專案路徑 | `/data/wwwroot/yescialis.com/public` |
| 環境 | 原生 Ubuntu（PHP 7.4） |
| 資料庫 | 本機 MySQL `yescialis`（root/3ns7jtwh） |
| 備註 | 此為舊站（Laravel 10），v1 新站測試中 |

### Test（測試環境）

| 項目 | 內容 |
|------|------|
| 測試域名 | **slir?**.top（待新增 / 或共用 slir7.top 新路徑） |
| 伺服器 | 45.148.120.52（1Panel Docker） |
| SSH 金鑰 | `~/.ssh/small-summer` 或 `~/workspace/wwwroot/hk-server-keys/deploy_key` |
| 遠端路徑 | 待建立（建議 `/opt/1panel/www/sites/slir?.top/index/`） |
| Git 部署 | `git pull origin main`（remote: `git@github.com:kangxiang242/yesc-v1.git`） |
| PHP | Docker 容器 `php82`（`docker exec -w <path> php82 php artisan ...`） |
| Nginx | Docker 容器 `1Panel-openresty-UOYX` |
| 資料庫 | 共享 5.182.210.43:3306 / 需建立獨立 DB `yescialis_v1` |
| CF 帳號 | `aqs33202@outlook.com`（#11 通用/測試） |
| 快取清除 | `docker exec php82 php artisan optimize:clear` + `docker exec php82 php -r 'opcache_reset();'` |

> ⚠️ **注意**：目前測試域名尚未部署 yesc-v1。slir7.top 正在運行 twshop-v1，需要新增測試域名（如 slir6.top）或新增 Nginx server block。

---

## 数据库

| 项目 | 值 |
|------|-----|
| 本地数据库 | `yescialis`（root/root2312） |
| 共享测试库 | 5.182.210.43:3306（root/mariadb_2312）— 待建立 `yescialis_v1` |

---

## 关键配置文件

| 文件 | 用途 |
|------|------|
| `.env` | 数据库连接、APP_KEY、ADMIN_PATH |
| `config/global.php` | 全局常量配置（admin_path、image_url、cache keys） |
| `config/app.php` | APP_URL、M_URL、时区等 |
| `deploy/nginx-slir7.conf` | Nginx 配置参考（指向 slir7.top，需按实际测试域名调整） |

---

## Git 记录

```bash
# 远程仓库
origin  git@github.com:kangxiang242/yesc-v1.git (fetch)
origin  git@github.com:kangxiang242/yesc-v1.git (push)

# 最新 commits
2fea9b7  fix: OrderRepository 商品名称硬编码错误 — 改为从数据库 Product.name 读取
257b5ac  docs: 更新 Small Summer 服务器 SSH 改为密钥登录(密码已禁用)
```

## 已修复问题

| 日期 | 问题 | 修复 |
|------|------|------|
| 2026-07-09 | `OrderRepository.php:204` 商品名称硬编码为「威而鋼」(Viagra) | 改为 `$item->name` 从数据库动态读取 |

---

## CF账号

| 邮箱 | 说明 |
|------|------|
| `aqs33202@outlook.com` | #11 通用/测试，Zone: slir?.top |

---

## 变更记录

| 日期 | 内容 |
|------|------|
| 2026-07-09 | root.md 从 twshop-v1 改为 yesc-v1（Cialis）專屬內容 |
| 2026-07-09 | 修复 OrderRepository 商品名称硬编码错误 |
| 2026-07-07 | yesc-v1 仓库建立 + 首次推送（基于 twshop-v1 模板） |

---

## 参考

- 服务器文档：[test-45.148.120.52.md](/Users/a123/workspace/wwwroot/my-notes/香港集策/服务器/测试与备份/test-45.148.120.52.md)
- 香港集策笔记：`/Users/a123/workspace/wwwroot/my-notes/香港集策/`
- 原始模板项目：twshop-v1（V-viagraeshop/twshop-v1）
