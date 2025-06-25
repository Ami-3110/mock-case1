{{-- resources/views/purchase/thanks.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="thanks-container">
    <h1>ご購入ありがとうございました！</h1>
    <p>お支払いが完了しました。</p>
    <a href="{{ route('items.index') }}">トップへ戻る</a>
</div>
@endsection