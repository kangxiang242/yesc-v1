<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\JsonResponse;
use App\Services\VehicleService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class CustomerController extends Controller
{
    public function send(Request $request)
    {
        if (!$request->message) {
            return JsonResponse::make()->message('')->send();
        }

        $message = strtolower(trim($request->message));

        // 判斷人工客服關鍵詞
        $manual_customer_keyword = trim(app('cache.config')->get('manual_customer_keyword'));
        if ($message == strtolower($manual_customer_keyword)) {
            $manual_customer_text = app('cache.config')->get('manual_customer_text');
            return JsonResponse::make()->data(['message' => $manual_customer_text, 'type' => 'manual'])->send();
        }

        // 進入關鍵詞匹配
        $chat_robots = json_decode(app('cache.config')->get('chat_robots'), true);
        if ($chat_robots) {
            $chat_robots = array_values($chat_robots);
            $sort_arr = array_column($chat_robots, 'sort');
            array_multisort($sort_arr, SORT_DESC, $chat_robots);

            foreach ($chat_robots as $words) {
                $keyword = Arr::get($words, 'keyword');
                $is_matching = false;

                if (strpos($keyword, "&") !== false) {
                    $keywords = explode('&', $keyword);
                    $is_matching = true;
                    foreach ($keywords as $v) {
                        if ($v && !(strpos($message, strtolower(trim($v))) !== false)) {
                            $is_matching = false;
                            break;
                        }
                    }
                } elseif (strpos($keyword, "|") !== false) {
                    $keywords = explode('|', $keyword);
                    $is_matching = false;
                    foreach ($keywords as $v) {
                        if ($v && strpos($message, strtolower(trim($v))) !== false) {
                            $is_matching = true;
                            break;
                        }
                    }
                } else {
                    if ($keyword && strpos($message, strtolower(trim($keyword))) !== false) {
                        $is_matching = true;
                    }
                }

                if ($is_matching == true) {
                    return JsonResponse::make()->data(['message' => Arr::get($words, 'reply'), 'type' => 'message'])->send();
                }
            }
        }

        // 完全不匹配時回復
        return JsonResponse::make()->data(['message' => app('cache.config')->get('mismatch_text'), 'type' => 'message'])->send();
    }
}
