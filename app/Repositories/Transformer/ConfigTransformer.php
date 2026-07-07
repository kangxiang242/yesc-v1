<?php

namespace App\Repositories\Transformer;



class ConfigTransformer
{
    protected $value;

    public function __construct(string $value = null)
    {
        $this->value = $value;
    }

    /**
     * 转换为数组，如果是 JSON 字符串
     */
    public function toArray(): array
    {
        if (is_string($this->value) && $this->isJson($this->value)) {
            $decoded = json_decode($this->value, true);
            return json_last_error() === JSON_ERROR_NONE ? array_values($decoded) : (array)$this->value;
        }

        return (array)$this->value;
    }

    /**
     * 判断是否是 JSON 格式
     */
    protected function isJson(string $value): bool
    {
        json_decode($value);
        return json_last_error() === JSON_ERROR_NONE;
    }


    /**
     * 返回值
     *
     * @return string
     */
    public function value(): string
    {
        return (string) $this->value;
    }

    /**
     * 将对象转换为字符串输出
     *
     * @return string
     */
    public function __toString(): string
    {

        return (string) $this->value;
    }
}