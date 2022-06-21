<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait HasJsonResponse
{
    /**
     * Send Customized Response for ajax requests.
     * @param Validator $validator
     * @throws ValidationException
     * @throws HttpException
     */
    protected function failedValidation(Validator $validator)
    {
        if ($this->ajax()) {
            throw new HttpException(422, $validator->errors()->first());
        }
        parent::failedValidation($validator);
    }
}
