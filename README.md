# Viagra TW Shop

台灣威而鋼官方訂購網站，已從 Laravel 8 + Dcat Admin 遷移重建至 Laravel 11 + Filament 3。

## 系統環境

| 項目 | 版本 / 值 |
|------|----------|
| PHP | 8.2 |
| Laravel | 11.x |
| Filament | 3.3 |
| 數據庫 | MySQL/MariaDB |
| 數據庫名 | `viagra_twshop` |

## 本地啟動

```bash
php artisan serve --port=8012
```

> 必須使用 `localhost:8012` 訪問，使用 `127.0.0.1` 會觸發 `redirect.device` 中間件重定向。

## 地址

| 入口 | URL |
|------|-----|
| 前台首頁 | http://localhost:8012/ |
| 後台管理 | http://localhost:8012/admin |

## 後台帳號

登入頁已客製化支援「用戶名」或「郵箱」兩種方式登入。

| 欄位 | 值 |
|------|-----|
| 用戶名 | `web0wer16888` |
| 郵箱 | `web0wer16888@twshop.com` |
| 密碼 | `888d00rkeeper888` |

## 數據庫連接

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=viagra_twshop
DB_USERNAME=root
DB_PASSWORD=
```

## 主要架構

- **前台**：Blade 模板（web/ 桌面端 + mobile/ 移動端），通過 `template()` 輔助函數自動切換
- **後台**：Filament 3 Resources，路徑 `/admin`
- **登入頁**：自定義 [`Login`](app/Filament/Pages/Auth/Login.php)，支援用戶名/郵箱混合登入，覆蓋 `getCredentialsFromFormData()` 自動判斷輸入類型
- **中間件**：`redirect.device`（設備跳轉）、`googlebot.checked`（爬蟲檢測）、`access.log`（訪問日誌）、`defend`（防禦）
- **配置緩存**：`ConfigService` 使用 Laravel Cache facade，通過 `app('cache.config')` 全局調用
- **表單安全**：HMAC-SHA256 簽名 + AES-256-CBC 加密 + form_token 防重複提交
