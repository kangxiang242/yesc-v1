<?php

namespace App\Repositories;

use App\Models\Message;
use App\Services\VehicleService;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class MessageRepository extends Repository
{

    protected  $modelClass = Message::class;


    /**
     * 新增留言
     * @param array $data
     * @return mixed
     */
    public function store(array $data){
        $data = Arr::only($data,['name','email','phone','content','type','sex']);
        $insert = array_merge($data,['ip'=>VehicleService::IP(),'user_agent'=>VehicleService::userAgent()]);
        return $this->model()->create($insert);
    }

    /**
     * @return mixed
     */
    public function groupByDayIpCount(){
        return $this->model()->whereBetWeen('created_at',[Carbon::now()->startOfDay(),Carbon::now()->endOfDay()])->where('ip',VehicleService::IP())->count();
    }



}