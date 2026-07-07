<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper;
use App\Http\Requests\MessageRequest;
use App\Repositories\MessageRepository;
use App\Models\Faq;

class MessageController extends Controller
{
    public function index(){
        $faqs = Faq::where('status', 1)
            ->where('uri', 'message')
            ->orderBy('sort')
            ->get();
        return template('message', ['faqs' => $faqs]);
    }

    /**
     * 添加留言
     * @param MessageRequest $request
     * @param MessageRepository $messageRepository
     * @return false|\Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|string
     */
    public function store(MessageRequest $request,MessageRepository $messageRepository){
        try {
            if($messageRepository->groupByDayIpCount() >= 5){
                return ResponseHelper::make()->message('留言次數過多，請稍後再試')->success();
            }
            $messageRepository->store($request->all());
            return ResponseHelper::make()->message('您的留言我們已收到')->success();
        }catch (\Exception $exception){
            return ResponseHelper::make()->message('留言失敗，系統出現異常')->error();
        }

    }

}
