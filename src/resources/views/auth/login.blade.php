@extends('layouts.app2')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="auth-form">
    <h2>ログイン</h2>

    @if ($errors->any())
        <div class="error-messages">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label for="email">メールアドレス</label>
        <input type="email" name="email" id="email" required>

        <label for="password">パスワード</label>
        <input type="password" name="password" id="password" required>

        <button type="submit">ログイン</button>
    </form>
    <p class="auth-link">
        <a href="{{ route('register') }}">会員登録はこちら</a>
    </p>
</div>
@endsection
