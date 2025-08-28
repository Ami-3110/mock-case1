<?php

// app/Http/Requests/TradeMessageRequest.php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TradeMessageRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'body'  => ['required','string','max:400'],         // 本文：必須/400字
            'image' => ['nullable','file','mimes:jpeg,png'],    // 画像：jpeg/png
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => '本文を入力してください',
            'body.max'      => '本文は400文字以内で入力してください',
            'image.mimes'   => '「.png」または「.jpeg」形式でアップロードしてください',
        ];
    }
}
