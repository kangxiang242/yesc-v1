<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    private const STORE_API_BASE = 'https://www.slir2.top/api/regionstore';

    /**
     * 统一名称：API 使用简化字（台），前端可能用传统字（臺）
     */
    private static function normalizeName(string $name): string
    {
        return str_replace(['臺', '臺'], '台', $name);
    }

    /**
     * 调用 slir2.top API 获取地区/门店数据（后端代理模式）
     */
    private function callStoreApi(array $params = []): array
    {
        try {
            $client = new Client(['timeout' => 10, 'verify' => false]);
            $response = $client->get(self::STORE_API_BASE . '/linkage', ['query' => $params]);
            $result = json_decode((string) $response->getBody(), true);

            if (isset($result['code']) && $result['code'] === 1 && isset($result['data'])) {
                return $result['data'];
            }
        } catch (\Exception $e) {
            // 静默处理，API 不可用时返回空
        }

        return [];
    }

    /**
     * 获取城市列表
     */
    public function getCity(Request $request)
    {
        // type=0: 宅配, type=1: 7-11, type=2: 其他超商 — 全部走 slir2.top API
        $data = $this->callStoreApi();

        return response()->json(
            array_map(fn($item) => [
                'id'        => $item['id'],
                'parent_id' => 0,
                'pid'       => 0,
                'level'     => 1,
                'name'      => $item['name'],
            ], $data)
        );
    }

    /**
     * 获取区/县列表
     */
    public function getCounty(Request $request)
    {
        $cityName = trim($request->city_name ?? '');

        if (!$cityName) {
            return response()->json([]);
        }

        // 先查 city ID
        $cities = $this->callStoreApi();
        $cityNameNorm = static::normalizeName($cityName);
        $cityId = null;
        foreach ($cities as $c) {
            if (static::normalizeName($c['name']) === $cityNameNorm) {
                $cityId = $c['id'];
                break;
            }
        }

        if (!$cityId) {
            return response()->json([]);
        }

        $data = $this->callStoreApi(['city_id' => $cityId]);

        return response()->json(
            array_map(fn($item) => [
                'id'        => $item['id'],
                'parent_id' => $cityId,
                'pid'       => $cityId,
                'level'     => 2,
                'name'      => $item['name'],
            ], $data)
        );
    }

    /**
     * 获取路段列表
     */
    public function getRoad(Request $request)
    {
        $cityName   = trim($request->city_name ?? '');
        $countyName = trim($request->county_name ?? '');

        if (!$cityName || !$countyName) {
            return response()->json([]);
        }

        // 查 city ID
        $cities = $this->callStoreApi();
        $cityNameNorm = static::normalizeName($cityName);
        $cityId = null;
        foreach ($cities as $c) {
            if (static::normalizeName($c['name']) === $cityNameNorm) {
                $cityId = $c['id'];
                break;
            }
        }

        if (!$cityId) {
            return response()->json([]);
        }

        // 查 district ID
        $districts = $this->callStoreApi(['city_id' => $cityId]);
        $countyNameNorm = static::normalizeName($countyName);
        $districtId = null;
        foreach ($districts as $d) {
            if (static::normalizeName($d['name']) === $countyNameNorm) {
                $districtId = $d['id'];
                break;
            }
        }

        if (!$districtId) {
            return response()->json([]);
        }

        $data = $this->callStoreApi(['city_id' => $cityId, 'district_id' => $districtId]);

        return response()->json(
            array_map(fn($item) => [
                'id'        => $item['id'],
                'parent_id' => $districtId,
                'pid'       => $districtId,
                'level'     => 3,
                'name'      => $item['name'],
            ], $data)
        );
    }

    /**
     * 获取门店列表
     */
    public function getShop(Request $request)
    {
        $cityName   = trim($request->city_name ?? '');
        $countyName = trim($request->county_name ?? '');

        if (!$cityName || !$countyName) {
            return response("");
        }

        // 查 city ID
        $cities = $this->callStoreApi();
        $cityNameNorm = static::normalizeName($cityName);
        $cityId = null;
        foreach ($cities as $c) {
            if (static::normalizeName($c['name']) === $cityNameNorm) {
                $cityId = $c['id'];
                break;
            }
        }

        if (!$cityId) {
            return response("");
        }

        // 查 district ID
        $districts = $this->callStoreApi(['city_id' => $cityId]);
        $countyNameNorm = static::normalizeName($countyName);
        $districtId = null;
        foreach ($districts as $d) {
            if (static::normalizeName($d['name']) === $countyNameNorm) {
                $districtId = $d['id'];
                break;
            }
        }

        // 调用门店 API 获取该区域所有门店
        $params = ['city_id' => $cityId];
        if ($districtId) {
            $params['district_id'] = $districtId;
        }

        $data = $this->callStoreApi($params);

        return view('web.widgets.shopping-store-item', compact('data', 'cityName', 'countyName'))->render();
    }

    /**
     * 旧版 areas 表查询 — 保留兼容（部分旧组件可能直接调用）
     */
    public function get(Request $request)
    {
        $pid = $request->get('pid', 0);
        // 从 slir2.top 查询子级
        if ($pid == 0) {
            $data = $this->callStoreApi();
        } else {
            $data = $this->callStoreApi(['city_id' => $pid]);
        }

        return response()->json(
            array_map(fn($item) => [
                'id'        => $item['id'],
                'parent_id' => $pid,
                'pid'       => $pid,
                'level'     => 1,
                'name'      => $item['name'],
            ], $data)
        );
    }
}
