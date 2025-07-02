@extends('layouts.app2')
@section('css')
<link rel="stylesheet" href="{{ asset('css/verify.css') }}">
@endsection


@section('content')
    <div class="message-bg">
        <div class="message-field">
            <p class="message-1">
                登録していただいたメールアドレスに認証メールを送付しました。<br>
                メール認証を完了してください。
            </p>
            
            <a href="http://localhost:8025/" target="_blank" rel="noopener noreferrer" class="verify-btn">認証はこちらから</a>
                    
            <form method="POST" action="{{ route('verification.send') }}" class="resend-form">
                @csrf
                <button class="resend-link">認証メールを再送する</button>
            </form>
        </div>
    </div>
@endsection
