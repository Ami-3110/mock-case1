<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
//use Illuminate\Auth\Events\Registered;
//use Illuminate\Contracts\Auth\MustVerifyEmail;
//use Illuminate\Support\Facades\Event;
use Illuminate\Http\RedirectResponse;
use Laravel\Fortify\Fortify;
//use Laravel\Fortify\Contracts\VerifyEmailViewResponse;
use Laravel\Fortify\Contracts\RegisterResponse;
//use App\Http\Responses\VerifyEmailViewResponse as CustomVerifyEmailViewResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());
            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::registerView(function () {
            return view('auth.register');
        });

        // 会員登録後にプロフィール編集画面へ飛ばすカスタムリダイレクト
        $this->app->singleton(RegisterResponse::class, function () {
            return new class implements RegisterResponse {
                public function toResponse($request): RedirectResponse
                {
                    //遷移先設定
                    return redirect('/mypage/edit');
                }
            };
        });

// イベントリスナ登録（メール認証に必要）
//        Event::listen(Registered::class, function ($event) {
//            if ($event->user instanceof MustVerifyEmail && ! $event->user->hasVerifiedEmail()) {
//                session()->put('must_verify_email', true);
//            }
//        });
// Fortify が使うメール認証ページのカスタムView（今は使わない）
//        $this->app->singleton(VerifyEmailViewResponse::class, CustomVerifyEmailViewResponse::class);

// ユーザー登録後に「認証してください」ページへ飛ばす処理（今は使わない）
//        $this->app->singleton(RegisterResponse::class, function () {
//            return new class implements RegisterResponse {
//                public function toResponse($request): RedirectResponse
//                {
//                    return redirect()->route('verification.notice');
//                }
//            };
//        });
    }
}
