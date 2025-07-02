@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/thanks.css') }}">
@endsection

@section('content')
<div class="thanks-container">
    <h1>ご購入ありがとうございました！</h1>
    <p>お支払いが完了しました。</p>
    <a href="{{ route('items.index') }}">トップへ戻る</a>
</div>
@endsection