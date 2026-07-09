<?php

namespace App\Repositories;


use App\Exceptions\MsgException;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Services\VehicleService;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

class OrderRepository extends Repository
{

    protected  $modelClass = Order::class;


    /**
     * 通过订单号获取订单
     * @param $no
     * @return mixed
     */
    public function getByNo($no){
        return $this->model()->whereNo($no)->first();
    }

    /**
     * 通过电话邮箱获取订单
     * @param $phone
     * @param $email
     * @return mixed
     */
    public function getByPhoneEmail($phone,$email){
        return $this->model()->where('email',$email)->where('phone',$phone)->orderBy('id','desc')->first();
    }

    /**
     * 通过姓名電話獲取訂單
     * @param $phone
     * @param $name
     * @return mixed
     */
    public function getByNamePhone($name,$phone){
        return $this->model()->where('name',$name)->where('phone',$phone)->orderBy('id','desc')->first();
    }

    /**
     * 订单生成
     * @param array $data
     * @param array $products
     * @return mixed
     */
    public function store(array $data,$products){




        return DB::transaction(function () use($data,$products) {

            $ip_count = $this->getDayIpOrderCount(VehicleService::IP());

            if($ip_count>=5){
                //throw new MsgException("您今日下單數過多，請理性消費！");
            }


            $product_price = $this->countOrderPrice($products);

            $insert_data = [
                'no'=>$this->makeOrderNo(),
                'inside_no'=>$this->makeOrderInsideNo(),
                'ip'=>VehicleService::IP(),
                'ipcountry'=>request()->header('cf-ipcountry'),
                'user_agent'=>request()->header('user-agent'),
                'product_price'=>$product_price,
                'delivery_type'=>Arr::get($data,'order_type')
            ];

            if(Arr::get($data,'order_type') > 0){
                $store_id = Arr::get($data,'store_id');

                if(!$store_id){
                    throw new MsgException("便利店選擇有誤!");
                }
                // 使用 slir2.top API 获取门店信息（替代 StoreSynchronizing）
                $city_id = Arr::get($data,'city_id');
                $district_id = Arr::get($data,'county_id');
                $road_id = Arr::get($data,'street_id');

                if(Arr::get($data,'order_type') == 1){
                    // 7-11 - 使用 slir2.top API
                    try {
                        $client = new Client(['timeout' => 10, 'verify' => false]);
                        $apiUrl = 'https://www.slir2.top/api/regionstore/linkage';
                        $response = $client->get($apiUrl, [
                            'query' => [
                                'city_id' => $city_id,
                                'district_id' => $district_id,
                                'road_id' => $road_id
                            ]
                        ]);
                        $result = json_decode((string)$response->getBody(), true);

                        if($result && $result['code'] === 1 && !empty($result['data'])) {
                            $shop = null;
                            foreach($result['data'] as $item) {
                                if($item['id'] == $store_id) {
                                    $shop = [
                                        'shop_no' => $item['id'],
                                        'shop_name' => $item['store_name'] ?? $item['name'] ?? '',
                                        'shop_address' => $item['address'] ?? '',
                                    ];
                                    break;
                                }
                            }
                            if(!$shop) {
                                throw new MsgException("便利店信息有誤!");
                            }
                            $insert_data['address'] = $shop['shop_address'];
                            $insert_data['shop_no'] = $shop['shop_no'];
                            $insert_data['shop_name'] = $shop['shop_name'];
                            $insert_data['shop_type'] = 1; // 7-11
                            $insert_data['shop_data'] = $shop;
                        } else {
                            throw new MsgException("便利店信息有誤!");
                        }
                    } catch (MsgException $e) {
                        throw $e;
                    } catch (\Exception $e) {
                        throw new MsgException("便利店信息有誤!");
                    }
                }elseif(Arr::get($data,'order_type') == 2){
                    // ezship - 使用 slir2.top API（替代 StoreSynchronizing）
                    try {
                        $client = new Client(['timeout' => 10, 'verify' => false]);
                        $apiUrl = 'https://www.slir2.top/api/regionstore/linkage';
                        $response = $client->get($apiUrl, [
                            'query' => [
                                'city_id' => $city_id,
                                'district_id' => $district_id,
                                'road_id' => $road_id
                            ]
                        ]);
                        $result = json_decode((string)$response->getBody(), true);

                        $shop = null;
                        if($result && $result['code'] === 1 && !empty($result['data'])) {
                            foreach($result['data'] as $item) {
                                if(($item['id'] ?? '') == $store_id || ($item['shop_no'] ?? '') == $store_id) {
                                    $shop = [
                                        'shop_no' => $item['shop_no'] ?? $item['id'],
                                        'shop_name' => $item['store_name'] ?? $item['name'] ?? '',
                                        'shop_address' => $item['address'] ?? '',
                                        'shop_type' => 2,
                                    ];
                                    break;
                                }
                            }
                        }
                        if(!$shop) {
                            throw new MsgException("便利店信息有誤!");
                        }
                        $insert_data['address'] = $shop['shop_address'];
                        $insert_data['shop_no'] = $shop['shop_no'];
                        $insert_data['shop_name'] = $shop['shop_name'];
                        $insert_data['shop_type'] = $shop['shop_type'];
                        $insert_data['shop_data'] = $shop;
                    } catch (MsgException $e) {
                        throw $e;
                    } catch (\Exception $e) {
                        throw new MsgException("便利店信息有誤!");
                    }
                }else{
                    throw new MsgException("便利店數據有誤！");
                }
            }else{
                $insert_data['address'] = Arr::get($data,'address');
            }

            $freight_where = \App\Services\ConfigService::get('freight_where',0);
            if($product_price>$freight_where){
                $freight = 0;
            }else{
                $freight = \App\Services\ConfigService::get('freight',0);
            }
            $insert_data['freight'] = $freight;
            $insert_data['total_price'] = $freight+$product_price;
            $insert_data2 = Arr::only($data,['name','phone','email','city','county','street','remarks','delivery_time']);
            $insert_data = array_merge($insert_data,$insert_data2);
            $insert_data['phone'] = str_replace('-','',$insert_data['phone']);
            $order = $this->model()->create($insert_data);

            $order_product = [];
            $time = date('Y-m-d H:i:s');
            foreach ($products as $item){
                $num = 1;
                $order_product[] = [
                    'order_id'=>$order->id,
                    'product_id'=>$item->id,
                    'product_name'=>$item->name,
                    'product_img'=>$item->img,
                    'number'=>$num,
                    'unit_price'=>$item->price,
                    'total_price'=>$num*$item->price,
                    'product'=>$item->toJson(),
                    'created_at'=>$time,
                    'updated_at'=>$time,
                ];

            }


            if($order_product){
                OrderProduct::insert($order_product);
            }else{
                throw new MsgException("提交失敗，提交數據有誤");
            }
            return $order;
        });


    }

    /**
     * 获取商品信息
     * @param array $ids
     * @return mixed
     */
    public function getProducts($ids = []){
        return Product::whereIn('id',$ids)->where('status',1)->get();
    }

    /**
     * 计算商品总价格
     * @param $products
     * @return float|int
     */
    public function countOrderPrice($products){
        $product_total_price = 0;
        foreach($products as $item){

            $product_total_price += $item->price;


        }

        return $product_total_price;
    }

    /**
     * 根據IP獲取今日下單數
     * @param $ip
     * @return mixed
     */
    public function getDayIpOrderCount($ip){
        return $this->model()->whereBetWeen('created_at',[Carbon::now()->startOfDay(),Carbon::now()->endOfDay()])->where('ip',$ip)->count();
    }


    /**
     * 生成订单号
     * @return string
     */
    public function makeOrderNo(){
        $no = date('YmdHi').rand(1000,9999);
        $order = $this->model()->where('no',$no)->first();
        if($order){
            $this->makeOrderNo();
        }
        return $no;
    }

    /**
     * 生成内部订单号
     * @return string
     */
    public function makeOrderInsideNo(){
        $count = $this->model()->whereBetWeen('created_at',[Carbon::now()->startOfDay(),Carbon::now()->endOfDay()])->count();
        return 'R1-'.date('YmdHi').'-'.($count+1);
    }



}