<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class BaseFormRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        if ($this->expectsJson()) {
            $errors = (new ValidationException($validator))->errors();
            $firstError = (collect(array_values($errors))->flatten()->first());
            throw new HttpResponseException(
                response()->error(
                    Response::HTTP_UNPROCESSABLE_ENTITY,
                    $firstError
                )
            );
        }

        parent::failedValidation($validator);
    }
}
