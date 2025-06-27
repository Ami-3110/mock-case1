<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Auth\MustVerifyEmail;


class RegisteredUserController extends Controller
{
    // 会員登録フォーム表示
    public function create(){
        return view('auth.register');
    }

    // 会員登録処理
    public function store(RegisterRequest $request){
        $user = User::create([
            'user_name' => $request->user_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($user instanceof MustVerifyEmail) {
        $user->sendEmailVerificationNotification();
        }

        Auth::login($user);

        return redirect()->route('verification.notice');

    }
}
