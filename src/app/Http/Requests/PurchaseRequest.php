<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
        $route = $this->route()->getName();

        if ($route === 'purchase.purchase') {
            return [
                'payment_method'=> ['required'],
            ];
        }
        if ($route === 'purchase.updateAddress') {
            return [
                'ship_postal_code' => ['required', 'regex:/^\d{3}-\d{4}$/'],
                'ship_address' => ['required'],
                'ship_building' => ['nullable'],
            ];
        }
        return[];

    }


    public function messages(){
        return [
            'payment_method.required' => '支払い方法を選択してください',
            'ship_postal_code.required' => '配送先の郵便番号が未入力です',
            'ship_postal_code.regex' => '郵便番号は-を含む8文字で入力してください',
            'ship_address.required' => '配送先の住所が未入力です',
        ];
    }
}
