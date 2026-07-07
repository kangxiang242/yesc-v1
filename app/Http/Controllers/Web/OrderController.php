<?php

namespace App\Http\Controllers\Web;

use App\Exceptions\MsgException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseHelper;
use App\Http\Requests\OrderStoreRequest;
use App\Models\Product;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Validator;
class OrderController extends Controller
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * OrderController constructor.
     * @param OrderRepository $orderRepository
     */
    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }


    /**
     * 订单查询
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function check(Request $request){

        if($request->isMethod('POST')){
            $checkType = $request->input('check_type', 'order_id');

            if($checkType === 'order_id'){
                // 订单号查询
                $orderNo = trim($request->input('order_id', ''));
                if(!$orderNo){
                    return ResponseHelper::make()->code(422)->message('請填寫訂單編號')->error();
                }
                $order = $this->orderRepository->getByNo(str_replace(' ', '', $orderNo));
            }else{
                // 联络资讯查询
                $validator = Validator::make($request->all(), [
                    'email' => 'required|email',
                    'phone' => 'required',
                ],[
                    'email.required'=>'請填寫電子信箱',
                    'email.email'=>'電子信箱格式錯誤',
                    'phone.required'=>'請填寫訂購電話',
                ]);

                if ($validator->fails()) {
                    $errors = $validator->errors();
                    return ResponseHelper::make()->code(422)->message($errors->first())->error();
                }

                $order = $this->orderRepository->getByPhoneEmail(str_replace(' ','',$request->phone),$request->email);
            }

            if($order){
                return ResponseHelper::make()->redirect(url('check/'.$order->no.'?source=check'))->success();
            }else{
                return ResponseHelper::make()->message('您所查詢的訂單不存在')->error();
            }
        }

        return template('order.check');
    }



    /**
     * 订单查询成功页
     * @param $no
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function checking($no){

        $order = $this->orderRepository->getByNo($no);

        if(!$order){
            abort(404);
        }
        return template('order.show',compact('order'));
    }

    /**
     * 下单成功页
     *
     * @param $no
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function succeed($no)
    {
        $order = $this->orderRepository->getByNo($no);

        if(!$order){
            abort(404);
        }
        return template('order.succeed',compact('order'));
    }

    /**
     * 订单结算页
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function checkout($id,Request $request){

        $product = ProductRepository::make()->all();

        $goods = $product->where('id',$id)->first();

        if (!$goods){
            abort(404);
        }


        //token 防止多次提交
        $form_token = md5(time());
        //将token存入session
        $request->session()->put('form_token',$form_token);
        return template('order.checkout',compact('form_token','goods','product'));
    }


    /**
     * 订单提交
     * @param OrderStoreRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(OrderStoreRequest $request){
        try {

            $form_token = $request->input('form_token');
            if( !$request->session()->get('form_token') || $request->session()->get('form_token')!=$form_token ){
                return ResponseHelper::make()->message("請勿重複送出")->error();
            }
            $request->session()->put('form_token',null);

            $products = Product::where('id',$request->goods_id)->where('status',1)->get();
            if(!$products || $products->isEmpty()){
                return ResponseHelper::make()->message("商品数据有误")->error();
            }
            $order = $this->orderRepository->store($request->all(),$products);
            return ResponseHelper::make()->message("訂單提交成功")->redirect('/check/'.$order->no)->flash()->success();
        }catch (\Exception $exception){
            return ResponseHelper::make()->message("系統出現異常：" . $exception->getMessage())->error();
        }

    }
}
