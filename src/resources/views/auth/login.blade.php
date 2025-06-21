@extends('layouts.app2')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="auth-form">
    <h2>ログイン</h2>

    @if ($errors->has('login_error'))
        <p class="error-message">{{ $errors->first('login_error') }}</p>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label for="email">メールアドレス</label>
        <input type="email" name="email" id="email">
        @error('email')
        <div class="error">{{ $message }}</div>
        @enderror

        <label for="password">パスワード</label>
        <input type="password" name="password" id="password">
        @error('password')
        <div class="error">{{ $message }}</div>
        @enderror

        <button type="submit">ログイン</button>
    </form>
    <p class="auth-link">
        <a href="{{ route('register') }}">会員登録はこちら</a>
    </p>
</div>
@endsection
