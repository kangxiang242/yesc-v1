<?php

namespace App\Http\Controllers;

use App\Services\BuyerMessageService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BuyerMessageController extends Controller
{
    /**
     * 购买消息服务
     */
    private $buyerMessageService;

    /**
     * 构造函数
     */
    public function __construct(BuyerMessageService $buyerMessageService)
    {
        $this->buyerMessageService = $buyerMessageService;
    }

    /**
     * 获取各盒数的购买人数
     */
    public function getBoxBuyers(): JsonResponse
    {
        $boxBuyers = $this->buyerMessageService->getBoxBuyersCount();

        return response()->json([
            'success' => true,
            'data' => $boxBuyers,
        ]);
    }

    /**
     * 获取预渲染的购买人数显示
     */
    public function getBuyerCountHtml(): JsonResponse
    {
        $html = $this->buyerMessageService->getBuyerCountHtml();

        return response()->json([
            'success' => true,
            'data' => $html,
        ]);
    }

    /**
     * 确认消息已显示并增加购买人数
     */
    public function confirmMessage(Request $request): JsonResponse
    {
        $request->validate([
            'boxNum' => 'required|integer|min:1',
        ]);

        $boxNum = $request->input('boxNum');
        $newBuyers = $this->buyerMessageService->confirmAndIncrementBuyer($boxNum);

        return response()->json([
            'success' => true,
            'data' => [
                'boxNum' => $boxNum,
                'boxBuyers' => $newBuyers,
            ],
        ]);
    }

    /**
     * 获取下一条购买消息
     */
    public function getNextMessage(): JsonResponse
    {
        $result = $this->buyerMessageService->getNextMessage();
        
        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }

    /**
     * 增加购买人数
     */
    public function incrementBuyer(Request $request): JsonResponse
    {
        $request->validate([
            'boxNum' => 'required|integer|min:1',
        ]);

        $boxNum = $request->input('boxNum');
        $newCount = $this->buyerMessageService->incrementBoxBuyer($boxNum);
        
        return response()->json([
            'success' => true,
            'data' => [
                'boxNum' => $boxNum,
                'count' => $newCount,
            ],
        ]);
    }

    /**
     * 更新配置（管理功能）
     */
    public function updateConfig(Request $request): JsonResponse
    {
        $request->validate([
            'totalBuyers' => 'integer|min:0',
            'boxPercentages' => 'array',
            'timeSlots' => 'array',
            'stayDuration' => 'integer|min:0',
        ]);

        $config = $request->only([
            'totalBuyers',
            'boxPercentages',
            'timeSlots',
            'stayDuration',
        ]);

        $this->buyerMessageService->setConfig($config);

        return response()->json([
            'success' => true,
            'message' => '配置已更新',
        ]);
    }

    /**
     * 刷新购买人数数据（清空缓存并重新计算）
     */
    public function refreshBuyers(): JsonResponse
    {
        $newBuyers = $this->buyerMessageService->refreshBoxBuyers();

        return response()->json([
            'success' => true,
            'message' => '购买人数已刷新',
            'data' => $newBuyers,
        ]);
    }
}
