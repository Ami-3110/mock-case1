<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'product_name' =>['required'],
            'description'=> ['required', 'max:255'],
            'product_image'=> ['required','mimes:jpg,jpeg,png'],
            'category' => ['required', 'array', 'min:1'],
            'category.*' => ['exists:categories,id'], // 各要素が有効なIDか
            'condition' => ['required'],
            'price' => ['required','integer','min:0'],
        ];
    }
    public function messages(){
        return [
            'product_name.required' => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明は255文字以内で入力してください',
            'product_image.required' => '商品画像をアップロードしてください',
            'product_image.mimes' => '商品画像は.jpg, .jpeg, .png形式でアップロードしてください',
            'category.required' => '商品のカテゴリーを１つ以上選択してください',
            'condition.required' => '商品の状態を選択してください',
            'price.required' => '商品価格を入力してください',
            'price.integer' => '商品価格は整数で入力してください',
            'priceired.min' => '商品価格は0円以上で入力してください',
        ];
    }
}
