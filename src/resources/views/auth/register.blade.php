
@extends('layouts.app2')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')

    <div class="container" style="max-width: 500px; margin: 50px auto;">
        <h2>会員登録</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div style="margin-bottom: 10px;">
                <label for="user_name">ユーザー名</label><br>
                <input type="text" name="user_name" id="user_name" value="{{ old('user_name') }}">
                @error('user_name')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 10px;">
                <label for="email">メールアドレス</label><br>
                <input type="email" name="email" id="email" value="{{ old('email') }}">
                @error('email')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 10px;">
                <label for="password">パスワード</label><br>
                <input type="password" name="password" id="password">
                @error('password')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-bottom: 10px;">
                <label for="password_confirmation">確認用パスワード</label><br>
                <input type="password" name="password_confirmation" id="password_confirmation" >
            </div>

            <button type="submit">登録する</button>
        </form>
        <p class="auth-link">
            <a href="{{ route('login') }}">ログインはこちら</a>
        </p>
    </div>
@endsection
