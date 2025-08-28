{{-- resources/views/emails/trade_completed.blade.php --}}
@component('mail::message')
# 取引完了のご連絡

{{ $trade->buyer->user_name ?? '購入者' }} さんが  
商品「**{{ $trade->product->product_name }}**」の**取引完了**ボタンを押しました。

取引チャットから、購入者への評価をお願いします。

@component('mail::button', ['url' => route('trades.show', $trade)])
取引チャットを開く
@endcomponent

※本メールはシステムより自動送信されています。

@endcomponent
