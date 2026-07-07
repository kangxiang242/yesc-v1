<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegionStoreController extends Controller
{
    /**
     * 711门店获取接口（代理 slir2.top，替代 StoreSynchronizing）
     * GET /api/regionstore/linkage
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function linkage(Request $request)
    {
        $cityId = $request->input('city_id');
        $districtId = $request->input('district_id');
        $roadId = $request->input('road_id');

        $params = [];
        if ($cityId !== null) $params['city_id'] = $cityId;
        if ($districtId !== null) $params['district_id'] = $districtId;
        if ($roadId !== null) $params['road_id'] = $roadId;

        $externalUrl = 'https://www.slir2.top/api/regionstore/linkage';

        try {
            $client = new \GuzzleHttp\Client([
                'timeout' => 10,
                'verify' => false,
            ]);
            $response = $client->get($externalUrl, ['query' => $params]);
            $body = (string) $response->getBody();
            $result = json_decode($body, true);

            if (json_last_error() === JSON_ERROR_NONE && isset($result['code'])) {
                return response()->json($result);
            }

            return response()->json([
                'code' => 1,
                'msg' => '获取成功',
                'time' => time(),
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code' => 0,
                'msg' => 'API请求失败: ' . $e->getMessage(),
                'time' => time(),
                'data' => []
            ]);
        }
    }

    /**
     * 代理接口 - 解决CORS问题
     * GET /api/regionstore/proxy
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function proxy(Request $request)
    {
        $cityId = $request->input('city_id');
        $districtId = $request->input('district_id');
        $roadId = $request->input('road_id');

        $params = [];
        if ($cityId) $params['city_id'] = $cityId;
        if ($districtId) $params['district_id'] = $districtId;
        if ($roadId) $params['road_id'] = $roadId;

        $externalUrl = 'https://www.slir2.top/api/regionstore/linkage';

        try {
            $client = new \GuzzleHttp\Client([
                'timeout' => 10,
                'verify' => false, // 跳过SSL验证
            ]);
            $response = $client->get($externalUrl, ['query' => $params]);
            $body = (string) $response->getBody();

            return response($body)
                ->header('Content-Type', 'application/json')
                ->header('Access-Control-Allow-Origin', '*');
        } catch (\Exception $e) {
            return response()->json([
                'code' => 0,
                'msg' => 'API请求失败: ' . $e->getMessage(),
                'time' => time(),
                'data' => []
            ]);
        }
    }
}