<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;

class LoginRequest extends FormRequest
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
            'email'=> ['required', 'email'],
            'password' => ['required','min:8'],
            'remember' => ['nullable'],

        ];
    }
    
    public function messages(){
        return [
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => 'メールアドレスは「ユーザー名@ドメイン」形式で入力してください',
            'password.required' => 'パスワードを入力してください',
            'password.min' => 'パスワードは8文字以上で入力してください',
        ];
    }

    public function authenticate(): void
    {
        $credentials = $this->only(Fortify::username(), 'password');

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            // フォーム全体に紐づくエラーを投げる（フィールド名なし）
            throw ValidationException::withMessages([
                'login_error' => 'ログイン情報が登録されていません。',
            ]);
        }
    }
}
