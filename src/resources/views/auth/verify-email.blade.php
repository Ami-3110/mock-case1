@extends('layouts.app2')
@section('css')
<link rel="stylesheet" href="{{ asset('css/verify.css') }}">
@endsection


@section('content')
    <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
        <h2 class="text-xl font-bold mb-4">メールアドレスを確認してください</h2>

        <p class="mb-4">
            登録していただいたメールアドレスに認証メールを送付しました。<br>
            メール認証を完了してください。
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="text-green-600 mb-4">
                新しい確認メールを送信しました！
            </div>
        @endif

        <form method="GET" action="{{ route('verification.notice') }}" style="margin-top: 10px;">
            <button type="submit" class="btn btn-secondary">認証はこちらから</button>
        </form>
        
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                認証メールを再送する
            </button>
        </form>
    </div>
@endsection
