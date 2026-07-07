<?php

namespace App\Http\Requests;

use App\Exceptions\ValidationFailedException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class BaseRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function failedValidation(Validator $validator)
    {
        throw new ValidationFailedException($validator->errors()->first());
    }
}
