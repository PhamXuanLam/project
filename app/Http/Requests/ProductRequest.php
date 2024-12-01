<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ProductRequest extends FormRequest
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
            'product_name' => "required",
            'description' => "nullable",
            'category_id' => "required|exists:categories,category_id",
            'min_price' => "required",
            'max_price' => "required",
            'variant_id' => "nullable", // Để kiểm tra nếu `variant_id` có giá trị
        ];

        if ($this->isMethod('post')) { // Kiểm tra nếu là phương thức POST (tạo mới)
            $rules['product_id'] = "required";
            $rules['seller_id'] = [
                'required',
                'exists:users,id', // Kiểm tra seller_id tồn tại trong cột id của bảng users
                function ($attribute, $value, $fail) {
                    if (!\App\Models\User::where('id', $value)->where('position', 'seller')->exists()) {
                        $fail('The selected seller must have a position as "seller".');
                    }
                },
            ];
        }
        if ($this->input('variant_id')) {
            $rules = array_merge($rules, [
                'variant_name' => "required",
                'color' => "nullable",
                'size'=> "nullable",
                'style'=> "nullable",
                'material'=> "nullable",
                'price' => "required",
                'stock_quantity' => "required",
                'is_active' => "nullable"
            ]);
        }

        return $rules;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
