@extends('layouts.app2')

@section('css')
<link rel="stylesheet" href="{{ asset('css/trade.css') }}">
@endsection

@section('content')
@php
  $me = auth()->user();
  $isBuyer = $role === 'buyer';
  $counter = $isBuyer ? $trade->seller : $trade->buyer;
  $counterName = $counter->user_name ?? $counter->name ?? 'ユーザー';
  $counterImage = $counter->userProfile->profile_image ?? null;
@endphp

<div class="trade-page trade-fullbleed">
    <div class="trade-layout">
    {{-- ===== 左カラム：その他の取引 ===== --}}
    <aside class="trade-sidebar">
        <div class="sidebar-head">その他の取引</div>

        <ul class="side-trade-list">
        @forelse($sideTrades as $t)
            <li class="side-trade-item {{ $t->id === $trade->id ? 'active' : '' }}">
            <a href="{{ route('trades.show', $t) }}" class="side-trade-link">
                <span class="side-trade-name">{{ $t->product->product_name }}</span>
            </a>
            </li>
        @empty
            <li class="side-empty">他の取引はありません</li>
        @endforelse
        </ul>
    </aside>

    {{-- ===== 右カラム：メイン ===== --}}
    <main class="trade-main">

        {{-- 上部：相手情報＋完了ボタン --}}
        <header class="main-head">
        <div class="head-left">
            <div class="avatar">
            @if ($counterImage)
                <img src="{{ Storage::disk('public')->url($counterImage) }}" alt="{{ $counterName }}">
            @endif
            </div>
            <h1 class="head-title">「{{ $counterName }}」さんとの取引画面</h1>
        </div>

        {{-- 完了ボタン（購入者のみ） --}}
        @if($role === 'buyer' && $trade->status === 'trading')
        <form method="POST" action="{{ route('trade.complete', $trade) }}">
            @csrf
            <button class="btn-complete">取引を完了する</button>
        </form>
        @endif
        </header>

        <div class="divider"></div>

        {{-- 商品情報ブロック --}}
        <section class="product-block">
        <div class="product-thumb">
            <img src="{{ asset('storage/' . $trade->product->product_image) }}" alt="商品画像">
        </div>
        <div class="product-info">
            <div class="product-name">{{ $trade->product->product_name }}</div>
            <div class="product-price">¥{{ number_format($trade->product->price) }}</div>
        </div>
        </section>

        <div class="divider"></div>

        {{-- チャット欄 --}}
        <section class="chat-area">
        @foreach($messages as $m)
            @php
            $mine = $m->user_id === $me->id;
            $u = $m->user;
            $uName = $u->user_name ?? $u->name ?? 'ユーザー';
            $uImage = $u->userProfile->profile_image ?? null;
            @endphp

            <div class="chat-row {{ $mine ? 'right' : 'left' }}">
            @if(!$mine)
                <div class="avatar small">
                @if ($uImage)
                    <img src="{{ Storage::disk('public')->url($uImage) }}" alt="{{ $uName }}">
                @endif
                </div>
            @endif

            <div class="bubble-wrap">
                <div class="name">{{ $uName }}</div>
                <div class="bubble">
                @if($m->image_path)
                    <div class="msg-image">
                    <img src="{{ Storage::disk('public')->url($m->image_path) }}" alt="添付画像">
                    </div>
                @endif
                <div class="msg-body">{{ $m->body }}</div>
                </div>

                @if($mine)
                <div class="ops">
                    {{-- ルート名は実装に合わせて変更してね --}}
                    <form method="POST" action="{{ route('chat.update', $m) }}" class="inline">
                    @csrf @method('PATCH')
                    <input type="hidden" name="body" value="{{ $m->body }}">
                    <button class="op-link">編集</button>
                    </form>
                    <form method="POST" action="{{ route('chat.destroy', $m) }}" class="inline" onsubmit="return confirm('削除しますか？')">
                    @csrf @method('DELETE')
                    <button class="op-link danger">削除</button>
                    </form>
                </div>
                @endif
            </div>

            @if($mine)
                <div class="avatar small">
                @if ($me->userProfile && $me->userProfile->profile_image)
                    <img src="{{ Storage::disk('public')->url($me->userProfile->profile_image) }}" alt="{{ $me->user_name }}">
                @endif
                </div>
            @endif
            </div>
        @endforeach
        </section>

        {{-- 入力フォーム --}}
        <form class="composer" method="POST" action="{{ route('chat.store', $trade) }}" enctype="multipart/form-data">
        @csrf
        @if ($errors->any())
            <div class="form-errors">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
            </div>
        @endif

        <div class="composer-row">
            <textarea name="body" rows="1" class="composer-input" placeholder="取引メッセージを記入してください">{{ old('body') }}</textarea>

            <label class="btn-image">
            画像を追加
            <input type="file" name="image" accept=".jpeg,.jpg,.png" hidden>
            </label>

            <button type="submit" class="btn-send" title="送信">
            <img src="{{ asset('images/send.jpg') }}" alt="送信">
            </button>
        </div>
        </form>

    </main>
    </div>
</div>
@section('js')
<script>
  (function(){
    const area = document.querySelector('.chat-area');
    if (area) area.scrollTop = area.scrollHeight;
  })();
</script>
@endsection

{{-- 評価モーダル（自分が未評価のときだけ表示） --}}
@php
  $me = auth()->id();
  $iAmBuyer  = $trade->buyer_id === $me;
  $iAmSeller = $trade->seller_id === $me;
  $iRated    = \App\Models\TradeRating::where('trade_id',$trade->id)->where('rater_id',$me)->exists();

  $shouldOpen = false;
  // 購入者：完了直後 or buyer_completedで未評価
  if ($iAmBuyer && !$iRated && (session('completed') || $trade->status === 'buyer_completed')) {
      $shouldOpen = true;
  }
  // 出品者：buyer_completedで未評価
  if ($iAmSeller && !$iRated && $trade->status === 'buyer_completed') {
      $shouldOpen = true;
  }
@endphp

@if($shouldOpen)
<div class="modal-backdrop">
  <div class="modal">
    <h3>取引相手を評価</h3>
    <form method="POST" action="{{ route('trade.ratings.store', $trade) }}">
      @csrf
      <div class="stars">
        <label><input type="radio" name="score" value="5" required>5</label>
        <label><input type="radio" name="score" value="4">4</label>
        <label><input type="radio" name="score" value="3">3</label>
        <label><input type="radio" name="score" value="2">2</label>
        <label><input type="radio" name="score" value="1">1</label>
      </div>
      <div class="actions">
        <button type="submit" class="btn primary">評価を送信</button>
      </div>
    </form>
  </div>
</div>
@endif

@endsection
