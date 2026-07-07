<?php

namespace App\Http\Controllers;
use Illuminate\Http\JsonResponse;
class ResponseHelper
{
    protected string $status = 'success';
    protected string $message = '';
    protected array $data = [];
    protected ?string $redirect = null;
    protected int $code = 200;
    protected bool $flash = false;

    /**
     * 创建实例
     *
     * @return self
     */
    public static function make(): self
    {
        return new self();
    }

    /**
     * 设置状态
     *
     * @param string $status
     * @return $this
     */
    public function status(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * 设置提示信息
     *
     * @param string $msg
     * @return $this
     */
    public function message(string $msg): self
    {
        $this->message = $msg;
        return $this;
    }

    /**
     * 设置返回数据
     *
     * @param array $data
     * @return $this
     */
    public function data(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * 设置跳转
     *
     * @param string $url
     * @return $this
     */
    public function redirect(string $url): self
    {
        $this->redirect = $url;
        return $this;
    }

    /**
     * 设置状态码
     *
     * @param int $code
     * @return $this
     */
    public function code(int $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * 成功返回
     *
     * @return JsonResponse
     */
    public function success(): JsonResponse
    {
        $this->status = 'success';
        return $this->toJson();
    }

    /**
     * 失败返回
     *
     * @return JsonResponse
     */
    public function error(): JsonResponse
    {
        $this->status = 'error';
        return $this->toJson();
    }

    /**
     * 設置flash
     *
     * @return $this
     */
    public function flash(): self
    {
        $this->flash = true;
        return $this;
    }

    /**
     * 统一生成 JsonResponse
     *
     * @return JsonResponse
     */
    protected function toJson(): JsonResponse
    {
        $res = [
            'status' => $this->status,
            'message' => $this->message,
            'data' => $this->data,
        ];

        if ($this->redirect) {
            $res['redirect'] = $this->redirect;
        }

        if($this->flash){
            session()->flash('flash',$res);
        }

        return response()->json($res, $this->code);
    }
}