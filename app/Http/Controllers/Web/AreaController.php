<?php

namespace App\Http\Controllers\Web;

use App\Handlers\DeviceTypeHandlers;
use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    private $request_city_name;
    private $request_county_name;
    private $request_road_name;

    public function __construct(Request $request)
    {
        $this->request_city_name = $request->city_name?trim($request->city_name):"";

        $this->request_county_name = $request->county_name?trim($request->county_name):"";

        $this->request_road_name = $request->road_name?trim($request->road_name):"";
    }

    public function get(Request $request){
        $area = Area::where('parent_id',$request->get('pid',0))->where('is_special',0)->get()->toJson();
        return response()->json($area);
    }

    public function getCity(Request $request){
        if($request->type == 1){
            return response()->json([]);

        }elseif($request->type == 2){
            return response()->json([]);

        }else{
            $data = Area::where('parent_id',0)->where('is_special',0)->select(['id','parent_id as pid','level','name'])->get()->toJson();
        }
        return response()->json($data);
    }

    public function getCounty(Request $request){
        if($request->type == 1){
            return response()->json([]);
        }elseif($request->type == 2){
            return response()->json([]);

        }else{
            $city = Area::where('parent_id',0)->where('is_special',0)->where('name',$this->request_city_name)->select(['id','parent_id as pid','level','name'])->first();
            if(!$city){
                $data = '[]';
            }else{
                $data = Area::where('parent_id',$city->id)->where('is_special',0)->select(['id','parent_id as pid','level','name'])->get()->toJson();
            }
        }
        return response()->json($data);
    }

    public function getRoad(Request $request){
        if($request->type == 1){
            return response()->json([]);
        }elseif($request->type == 2){
            return response()->json([]);

        }else{
            $city = Area::where('parent_id',0)->where('is_special',0)->where('name',$this->request_city_name)->select(['id','parent_id as pid','level','name'])->first();
            if(!$city){
                $data = '[]';
            }else{
                $county = Area::where('parent_id',$city->id)->where('is_special',0)->where('name',$this->request_county_name)->select(['id','parent_id as pid','level','name'])->first();
                $data = $county
                    ? Area::where('parent_id',$county->id)->where('is_special',0)->select(['id','parent_id as pid','level','name'])->get()->toJson()
                    : '[]';
            }
        }
        return response()->json($data);
    }

    public function getShop(Request $request){
        $data = [];
        if($request->type == 1){
            return response("");
        }elseif($request->type == 2){
            return response("");
        }

        $city_name = $this->request_city_name;
        $county_name = $this->request_county_name;

        return view('web.widgets.shopping-store-item',compact('data','city_name','county_name'))->render();

    }


}
