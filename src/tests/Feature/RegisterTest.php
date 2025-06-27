<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->post('/logout');
    }

    // 名前が入力されていない場合、バリデーションエラーになる
    public function test_validationErrorOccursWhenNameIsNotProvided()
    {
        $response = $this->post('/register', [
            'user_name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['user_name'=>'お名前を入力してください']);
    }

    // メールアドレスが入力されていない場合、バリデーションエラーになる
    public function test_validationErrorOccursWhenEmailIsNotProvided()
    {
        $response = $this->post('/register', [
            'user_name' => 'test user',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email'=>'メールアドレスを入力してください']);
    }

    // パスワードが入力されていない場合、バリデーションエラーになる
    public function test_validationErrorOccursWhenPasswordIsNotProvided()
    {
        $response = $this->post('/register', [
            'user_name' => 'test user',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['password'=>'パスワードを入力してください']);
    }

    // パスワードが7文字以下の場合、バリデーションエラーになる
    public function test_validationErrorOccursWhenPasswordIsTooShort()
    {
        $response = $this->post('/register', [
            'user_name' => 'test user',
            'email' => 'test@example.com',
            'password' => 'pass123',
            'password_confirmation' => 'pass123',
        ]);

        $response->assertSessionHasErrors(['password'=>'パスワードは8文字以上で入力してください']);
    }

    // パスワードが確認用パスワードと一致しない場合、バリデーションエラーになる
    public function test_validationErrorOccursWhenPasswordDoesNotMatchConfirmation()
    {
        $response = $this->post('/register', [
            'user_name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password456',
        ]);

        $response->assertSessionHasErrors(['password'=>'パスワードと一致しません']);
    }

    // 全ての項目が入力されている場合、会員情報が登録され、ログイン画面に遷移される
    public function test_registrationSucceedsAndRedirectsToLoginWhenAllFieldsAreProvided()
    {
        $response = $this->post('/register', [
            'user_name' => 'test user', 
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/email/verify');

        $this->assertDatabaseHas('users', [
            'user_name' => 'test user',
            'email' => 'test@example.com',
        ]);
    }
}
