<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserCreateReqest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'min:3', 'max:70'],
            'email' => ['required', 'unique:users,email', 'email'],
            'phone' => ['required', 'min:8', 'max:20'],
            'password' => ['required', 'min:6', 'max:30', 'confirmed'],
            'password_confirmation' => ['required', 'min:6', 'max:30'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response([
            'erros' => $validator->getMessageBag()
        ], 400));
    }
}
