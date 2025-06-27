<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->post('/logout');
    }

    // メールアドレスが入力されていない場合、バリデーションエラーになる
    public function test_validationErrorOccursWhenEmailIsNotProvided()
    {
        $response = $this->post('/login', [
            'email' => '', 
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email'=>'メールアドレスを入力してください']);
    }

    // パスワードが入力されていない場合、バリデーションエラーになる
    public function test_validationErrorOccursWhenPasswordIsNotProvided()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '', 
        ]);

        $response->assertSessionHasErrors(['password'=>'パスワードを入力してください']);
    }

    // 入力情報が間違っている場合、バリデーションエラーになる
    public function test_validationErrorOccursWhenInputIsInvalid()
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);
    
        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([ 'login_error' => 'ログイン情報が登録されていません。']);
    }

    // 正しい情報が入力された場合、ログイン処理が実行される
    public function test_loginProcessExecutesWithValidCredentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    // ログアウトができる
    public function test_userCanLogoutSuccessfully()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }

}
