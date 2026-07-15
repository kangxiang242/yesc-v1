<?php

namespace App\Services;

use App\Models\Release;
use Illuminate\Support\Facades\Cache;

/**
 * Release Token 映射派生服务（doc/RELEASE_TOKEN.md）
 *
 * 根据 page_view 上报的 html_token / asset_tokens 派生：
 *   release_version, release_deployed_at, release_status, asset_token_status
 *
 * release_status：
 *   - missing：无 html_token
 *   - invalid：不符合 ^[a-z][a-z0-9]{11}$
 *   - known：manifest 查到
 *   - unknown：格式对但查无历史
 *
 * asset_token_status：
 *   - ok：asset_missing_token_count=0 且 asset_tokens 全等于 html_token
 *   - missing：asset_missing_token_count > 0
 *   - mismatch：存在 ≠ html_token 的 asset token
 *   - not_reported：props 无这些字段
 */
class ReleaseMappingService
{
    private const TOKEN_REGEX = '/^[a-z][a-z0-9]{11}$/';
    private const CACHE_TTL = 300; // 5 min

    /**
     * 派生 release 映射字段
     *
     * @param array|null $props page_view 的 props
     * @return array{release_token:?string,release_version:?string,release_deployed_at:?string,release_status:?string,asset_token_status:?string}
     */
    public function derive(?array $props): array
    {
        $htmlToken = is_array($props) ? ($props['html_token'] ?? null) : null;
        $result = [
            'release_token'       => $htmlToken,
            'release_version'     => null,
            'release_deployed_at' => null,
            'release_status'      => null,
            'asset_token_status'  => null,
        ];

        // release_status 判定
        if (empty($htmlToken)) {
            $result['release_status'] = 'missing';
        } elseif (!preg_match(self::TOKEN_REGEX, $htmlToken)) {
            $result['release_status'] = 'invalid';
        } else {
            $release = $this->findByToken($htmlToken);
            if ($release) {
                $result['release_version']     = $release->version;
                $result['release_deployed_at'] = $release->deployed_at;
                $result['release_status']      = 'known';
            } else {
                $result['release_status'] = 'unknown';
            }
        }

        // asset_token_status 判定
        $result['asset_token_status'] = $this->deriveAssetStatus($props, $htmlToken);

        return $result;
    }

    /**
     * 派生 asset_token_status
     */
    private function deriveAssetStatus(?array $props, ?string $htmlToken): string
    {
        if (!is_array($props)) {
            return 'not_reported';
        }

        $hasAssetFields = array_key_exists('asset_missing_token_count', $props)
            || array_key_exists('asset_tokens', $props)
            || array_key_exists('asset_count', $props);

        if (!$hasAssetFields) {
            return 'not_reported';
        }

        $missingCount = (int)($props['asset_missing_token_count'] ?? 0);
        if ($missingCount > 0) {
            return 'missing';
        }

        $assetTokens = $props['asset_tokens'] ?? [];
        if (!is_array($assetTokens)) {
            $assetTokens = [];
        }

        // 全等于 html_token → ok；否则 mismatch
        if (empty($assetTokens)) {
            return 'ok';
        }

        foreach ($assetTokens as $t) {
            if ($t !== $htmlToken) {
                return 'mismatch';
            }
        }

        return 'ok';
    }

    /**
     * 按 token 查 Release（带缓存，避免 page_view 高频查询打穿 DB）
     */
    private function findByToken(string $token): ?Release
    {
        $cacheKey = 'release:token:' . $token;

        $releaseId = Cache::get($cacheKey);
        if ($releaseId === false) {
            // 已知 "不存在" 的负缓存
            return null;
        }
        if ($releaseId) {
            return Release::find($releaseId);
        }

        $release = Release::where('token', $token)->first();
        if ($release) {
            Cache::put($cacheKey, $release->id, self::CACHE_TTL);
        } else {
            Cache::put($cacheKey, false, self::CACHE_TTL);
        }

        return $release;
    }
}
