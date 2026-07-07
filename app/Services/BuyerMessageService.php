<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class BuyerMessageService
{
    /**
     * 配置参数
     */
    private $config;

    /**
     * 缓存过期时间（秒）
     */
    private $cacheExpiration;

    /**
     * 缓存键名
     */
    const CACHE_KEY_BOX_BUYERS = 'buyer_message:box_buyers';
    const CACHE_KEY_PERCENTAGES = 'buyer_message:percentages';
    const CACHE_KEY_TIMESTAMP = 'buyer_message:timestamp';
    const CACHE_KEY_TOTAL = 'buyer_message:total_buyers';

    public function __construct()
    {
        // 从配置文件加载配置
        $this->config = $this->loadConfig();
        // 缓存过期时间（从配置文件读取，默认24小时）
        $this->cacheExpiration = config('buyer-message.cacheExpiration', 86400);
    }

    /**
     * 加载配置
     */
    private function loadConfig()
    {
        return [
            'totalBuyers' => config('buyer-message.totalBuyers', 0),
            'boxPercentages' => config('buyer-message.boxPercentages', [1 => 100]),
            'timeSlots' => config('buyer-message.timeSlots', []),
            'stayDuration' => config('buyer-message.stayDuration', 3000),
        ];
    }

    /**
     * 设置配置
     */
    public function setConfig($config)
    {
        $this->config = array_merge($this->config, $config);
        // 清除缓存以应用新配置
        $this->clearCache();
    }

    /**
     * 初始化或获取购买人数数据
     */
    private function getBoxBuyers()
    {
        $buyers = Cache::get(self::CACHE_KEY_BOX_BUYERS);
        $timestamp = Cache::get(self::CACHE_KEY_TIMESTAMP);

        // 如果缓存不存在或已过期，重新计算
        if (!$buyers || !$timestamp || (time() - $timestamp > $this->cacheExpiration)) {
            $percentages = $this->selectPercentagesFromRanges();
            $buyers = $this->calculateBuyers($percentages);
            $this->saveToCache($buyers, $percentages, time());
        } else {
            // 加载百分比数据以构建加权池
            $percentages = Cache::get(self::CACHE_KEY_PERCENTAGES);
            if (!$percentages) {
                $percentages = $this->selectPercentagesFromRanges();
                $this->saveToCache($buyers, $percentages, $timestamp);
            }

            // 确保所有配置的盒数都有购买人数数据
            $configBoxNums = array_keys($this->config['boxPercentages'] ?? []);
            foreach ($configBoxNums as $boxNum) {
                if (!isset($buyers[$boxNum])) {
                    $buyers[$boxNum] = 0;
                }
            }
        }

        return $buyers;
    }

    /**
     * 从范围中随机选择百分比
     */
    private function selectPercentagesFromRanges()
    {
        $ranges = $this->config['boxPercentages'];
        $selectedPercentages = [];
        $total = 0;

        // 第一步：从每个范围中随机选择一个值
        foreach ($ranges as $boxNum => $range) {
            if (is_numeric($range)) {
                $selectedPercentages[$boxNum] = $range;
                $total += $range;
            } elseif (is_array($range) && isset($range['min']) && isset($range['max'])) {
                $min = ceil($range['min']);
                $max = floor($range['max']);
                $randomValue = rand($min, $max);
                $selectedPercentages[$boxNum] = $randomValue;
                $total += $randomValue;
            }
        }

        // 第二步：调整总和为100%（持续调整直到总和为100%）
        $difference = 100 - $total;
        $maxIterations = 100; // 防止无限循环
        $iterations = 0;

        while ($difference !== 0 && $iterations < $maxIterations) {
            arsort($selectedPercentages);
            $adjustStep = $difference > 0 ? 1 : -1;

            // 从大到小尝试调整
            foreach ($selectedPercentages as $boxNum => $value) {
                $newValue = $value + $adjustStep;
                $range = $ranges[$boxNum];

                if (is_numeric($range)) {
                    $selectedPercentages[$boxNum] = $newValue;
                    $difference += $adjustStep;
                } elseif (is_array($range) && isset($range['min']) && isset($range['max'])) {
                    $min = ceil($range['min']);
                    $max = floor($range['max']);
                    if ($newValue >= $min && $newValue <= $max) {
                        $selectedPercentages[$boxNum] = $newValue;
                        $difference += $adjustStep;
                    }
                }

                // 如果已达到目标，停止调整
                if ($difference === 0) {
                    break;
                }
            }

            $iterations++;
        }

        return $selectedPercentages;
    }

    /**
     * 计算各盒数的购买人数
     */
    private function calculateBuyers($percentages)
    {
        $boxBuyers = [];

        // 每种盒数的购买人数初始值在80-200之间（缓存到数据库，确保一段时间内一致）
        foreach ($percentages as $boxNum => $percentage) {
            $cacheKey = self::CACHE_KEY_BOX_BUYERS . ':' . $boxNum;
            $count = Cache::get($cacheKey);

            if (!$count || $count < 80 || $count > 200) {
                // 缓存不存在或过期时，随机生成新的购买人数
                $count = rand(80, 200);
                // 缓存24小时
                Cache::put($cacheKey, $count, $this->cacheExpiration);
            }

            $boxBuyers[$boxNum] = $count;
        }

        return $boxBuyers;
    }

    /**
     * 保存到缓存
     */
    private function saveToCache($buyers, $percentages, $timestamp)
    {
        Cache::put(self::CACHE_KEY_BOX_BUYERS, $buyers, $this->cacheExpiration);
        Cache::put(self::CACHE_KEY_PERCENTAGES, $percentages, $this->cacheExpiration);
        Cache::put(self::CACHE_KEY_TIMESTAMP, $timestamp, $this->cacheExpiration);
    }

    /**
     * 清除缓存
     */
    private function clearCache()
    {
        Cache::forget(self::CACHE_KEY_BOX_BUYERS);
        Cache::forget(self::CACHE_KEY_PERCENTAGES);
        Cache::forget(self::CACHE_KEY_TIMESTAMP);
        Cache::forget(self::CACHE_KEY_TOTAL);
    }

    /**
     * 构建加权随机池
     */
    private function buildWeightedPool($percentages)
    {
        $pool = [];

        foreach ($percentages as $boxNum => $percentage) {
            for ($i = 0; $i < $percentage; $i++) {
                $pool[] = $boxNum;
            }
        }

        return $pool;
    }

    /**
     * 根据占比随机选择盒数
     */
    private function selectBoxByPercentage($pool)
    {
        if (empty($pool)) {
            return 1;
        }
        
        $randomIndex = array_rand($pool);
        return $pool[$randomIndex];
    }

    /**
     * 获取当前时段配置
     */
    private function getCurrentTimeSlotConfig()
    {
        $hour = (int) date('H');
        $timeSlots = $this->config['timeSlots'] ?? [];

        // 如果有默认配置，且当前小时没有单独配置，使用默认配置
        if (isset($timeSlots['default']) && !isset($timeSlots[$hour])) {
            return $timeSlots['default'];
        }

        // 如果当前小时配置为0，不显示
        if (isset($timeSlots[$hour]) && $timeSlots[$hour] === 0) {
            return null;
        }

        // 如果有当前小时的配置，返回它
        if (isset($timeSlots[$hour]) && is_array($timeSlots[$hour])) {
            return $timeSlots[$hour];
        }

        // 默认返回标准配置
        return [
            'initialDelay' => 3000,
            'intervalMin' => 4000,
            'intervalMax' => 8000,
        ];
    }

    /**
     * API: 获取各盒数的购买人数
     */
    public function getBoxBuyersCount()
    {
        return $this->getBoxBuyers();
    }

    /**
     * API: 获取下一条购买消息（增加购买人数）
     */
    public function getNextMessage()
    {
        // 检查当前时段是否允许显示
        $timeSlotConfig = $this->getCurrentTimeSlotConfig();
        if (!$timeSlotConfig) {
            return [
                'shouldShow' => false,
                'message' => null,
                'nextInterval' => 0,
            ];
        }

        // 获取购买人数数据
        $boxBuyers = $this->getBoxBuyers();
        $percentages = Cache::get(self::CACHE_KEY_PERCENTAGES);

        // 构建加权池并随机选择盒数
        $pool = $this->buildWeightedPool($percentages);
        $boxNum = $this->selectBoxByPercentage($pool);

        // 生成随机手机号后三位
        $phone = rand(100, 999);

        // 增加购买人数
        if (!isset($boxBuyers[$boxNum])) {
            $boxBuyers[$boxNum] = 0;
        }
        $boxBuyers[$boxNum]++;

        // 保存更新后的数据
        Cache::put(self::CACHE_KEY_BOX_BUYERS, $boxBuyers, $this->cacheExpiration);

        // 生成下一次显示的间隔时间
        $nextInterval = rand($timeSlotConfig['intervalMin'], $timeSlotConfig['intervalMax']);

        // 生成预渲染的 HTML
        $messageHtml = '<p>09** *** <span class="update-phone">' . $phone . '</span> 剛完成訂購 <span class="update-num">' . $boxNum . '</span>盒</p>';

        return [
            'shouldShow' => true,
            'messageHtml' => $messageHtml,
            'boxNum' => $boxNum,
            'nextInterval' => $nextInterval,
            'boxBuyers' => $boxBuyers,
        ];
    }

    /**
     * API: 获取预渲染的购买人数显示
     */
    public function getBuyerCountHtml()
    {
        $boxBuyers = $this->getBoxBuyers();
        $html = [];

        foreach ($boxBuyers as $boxNum => $count) {
            $html[$boxNum] = '<p class="box-buyer-count" data-box-count="' . $boxNum . '">近24小時已有' . $count . '人訂購</p>';
        }

        return $html;
    }

    /**
     * API: 增加购买人数
     */
    public function incrementBoxBuyer($boxNum)
    {
        $boxBuyers = $this->getBoxBuyers();

        if (!isset($boxBuyers[$boxNum])) {
            $boxBuyers[$boxNum] = 0;
        }

        $boxBuyers[$boxNum]++;
        Cache::put(self::CACHE_KEY_BOX_BUYERS, $boxBuyers, $this->cacheExpiration);

        return $boxBuyers[$boxNum];
    }

    /**
     * 确认消息已显示并增加购买人数
     */
    public function confirmAndIncrementBuyer($boxNum)
    {
        $boxBuyers = $this->getBoxBuyers();

        if (!isset($boxBuyers[$boxNum])) {
            $boxBuyers[$boxNum] = 0;
        }

        $boxBuyers[$boxNum]++;
        Cache::put(self::CACHE_KEY_BOX_BUYERS, $boxBuyers, $this->cacheExpiration);

        return $boxBuyers;
    }

    /**
     * 强制刷新购买人数数据（清空缓存并重新计算）
     */
    public function refreshBoxBuyers()
    {
        $this->clearCache();
        $percentages = $this->selectPercentagesFromRanges();
        $buyers = $this->calculateBuyers($percentages);
        $this->saveToCache($buyers, $percentages, time());

        return $buyers;
    }
}
