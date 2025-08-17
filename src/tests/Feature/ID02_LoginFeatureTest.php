<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;

class ID02_LoginFeatureTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function email_required_validation_message_is_shown(): void
    {
        // 1. ログインページを開く
        $this->get('/login')->assertOk();

        // 2-3. メール未入力で送信
        $this->from('/login')->post('/login', [
            'email' => '',
            'password' => 'secret1234',
        ])
        ->assertRedirect('/login')
        ->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    #[Test]
    public function password_required_validation_message_is_shown(): void
    {
        $this->get('/login')->assertOk();

        $this->from('/login')->post('/login', [
            'email' => 'ami@example.com',
            'password' => '',
        ])
        ->assertRedirect('/login')
        ->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    #[Test]
    public function invalid_credentials_show_login_error(): void
    {
        $this->get('/login')->assertOk();

        $this->from('/login')->post('/login', [
            'email' => 'ami@example.com',
            'password' => 'wrongpass',
        ])
        ->assertRedirect('/login')
        ->assertSessionHasErrors([
            'login_error',
        ]);
    }

    #[Test]
    public function login_success_redirects_home(): void
    {
        $user = User::factory()->create([
            'email' => 'ami@example.com',
            'password' => Hash::make('secret1234'),
        ]);

        $this->post('/login', [
            'email' => 'ami@example.com',
            'password' => 'secret1234',
        ])
        ->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }
}
