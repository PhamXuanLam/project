<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Auth;

class CustomerRequest extends FormRequest
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
        $rules = [
            "username" => "required|unique:accounts,username," . Auth::id(),
            "email" => "required|email:rfc,dns|max:100|unique:accounts,email," . Auth::id(),
            "phone" => "required|string|min:10|max:12|unique:accounts,phone," . Auth::id(),
            'avatar' => "nullable",
            'role'=> "required|string",
            "address" => "nullable",
        ];

        if ($this->isMethod('post')) {
            $rules['password'] = 'required|confirmed';
        }

        return $rules;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
