<?php

namespace App\Http\Requests;

class OrderStoreRequest extends BaseRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'phone' => ['required', 'regex:/^09\d{2}\s\d{3}\s\d{3}$/'],
            'email' => 'required|email',
            'city' => ['required', 'not_in:0'],
            'county' => ['required', 'not_in:0'],
            'street' => ['required', 'not_in:0'],
            'goods_id' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => '請填寫收貨人姓名',
            'phone.required' => '請填寫收貨電話',
            'email.required' => '請填寫郵箱',
            'city.required' => '請選擇市/縣',
            'city.not_in' => '請選擇市/縣',
            'county.required' => '請選擇地區',
            'county.not_in' => '請選擇地區',
            'street.required' => '請選擇路段',
            'street.not_in' => '請選擇路段',
            'goods_id.required' => '商品数据错误',
            'phone.regex' => '請輸入正確的手機號格式（09XX XXX XXX）'
        ];
    }
}
