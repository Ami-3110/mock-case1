<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Fortify;

class AuthenticatedSessionController extends Controller
{
     // ログインフォーム表示用
     public function showLoginForm(){
         return view('auth.login');
     }

    // ログイン処理
    public function store(Request $request){
        $credentials = $request->validate([
            Fortify::username() => 'required|string',
            'password' => 'required|string',
        ]);
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            // ここでログイン後のリダイレクト先を指定
            return redirect()->intended('/'); 
        }
        return back()->withErrors([
            Fortify::username() => __('auth.failed'),
        ]);
    }

    // ログアウト処理
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
