<?php

namespace App\Console\Commands;

use App\Models\Release;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ReleaseStamp extends Command
{
    protected $signature = 'release:stamp
        {--bump= : 版本遞增級別：patch/minor/major}
        {--ver= : 指定版本號，例如 1.04}';

    protected $description = '產生新的 release token 並記錄版本資訊';

    public function handle()
    {
        // 1. 決定版本號
        $latest = Release::orderBy('deployed_at', 'desc')->first();
        $currentVersion = $latest ? $latest->version : '0.0';

        if ($this->option('ver')) {
            $newVersion = $this->option('ver');
        } elseif ($this->option('bump')) {
            $parts = explode('.', $currentVersion);
            $major = (int)($parts[0] ?? 0);
            $minor = (int)($parts[1] ?? 0);
            $patch = (int)($parts[2] ?? 0);

            switch ($this->option('bump')) {
                case 'major':
                    $major++;
                    $minor = 0;
                    $patch = 0;
                    break;
                case 'minor':
                    $minor++;
                    $patch = 0;
                    break;
                case 'patch':
                default:
                    $patch++;
                    break;
            }
            $newVersion = $major . '.' . sprintf('%02d', $minor);
            if ($patch > 0) {
                $newVersion .= '.' . $patch;
            }
        } else {
            // 默認 bump minor
            $parts = explode('.', $currentVersion);
            $major = (int)($parts[0] ?? 0);
            $minor = (int)($parts[1] ?? 0) + 1;
            $newVersion = $major . '.' . sprintf('%02d', $minor);
        }

        // 2. 產生 token
        $appKey = config('app.key');
        $deployedAt = now()->toIso8601String();
        $gitSha = '';
        try {
            $gitSha = trim(exec('git log --oneline -1 --format=%h 2>/dev/null') ?? '');
        } catch (\Exception $e) {
            // ignore
        }

        $raw = hash_hmac('sha256', $newVersion . '|' . $deployedAt . '|' . $gitSha, $appKey);
        // 轉小寫英數，取前12位，保證第一位是 a-z
        $alnum = preg_replace('/[^a-z0-9]/', '', strtolower($raw));
        $idx = abs(unpack('l', hash_hmac('md5', $raw, 'idx', true))[1] ?? 0) % 26;
        $token = chr(ord('a') + $idx) . substr($alnum, 0, 11);

        // 3. 新增記錄
        $release = Release::create([
            'version' => $newVersion,
            'deployed_at' => now(),
            'token' => $token,
            'git_sha' => $gitSha ?: null,
        ]);

        // 4. 清理 view/config cache，避免舊 token 繼續輸出
        Cache::flush();

        $this->info("✅ Release v{$newVersion} 已建立");
        $this->line("   Token: {$token}");
        $this->line("   Git:   {$gitSha}");
        $this->line("   時間:  {$deployedAt}");
    }
}
