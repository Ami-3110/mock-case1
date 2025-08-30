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

            {{-- 上部：相手情報 --}}
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

                    {{-- 操作ボタン＆編集フォーム --}}
                    @if($mine)
                    <div class="ops">
                        {{-- 編集 --}}
                        <button type="button" class="op-link js-edit-toggle" data-id="{{ $m->id }}">編集</button>

                        {{-- 空フォーム（表示位置の高さ調整でまどろっこしくなりました・・） --}}
                        <form id="del-{{ $m->id }}" method="POST" action="{{ route('chat.destroy', $m) }}">
                            @csrf
                            @method('DELETE')
                        </form>

                        <button class="op-link danger"
                                form="del-{{ $m->id }}"
                                onclick="return confirm('削除しますか？')">
                            削除
                        </button>
                    </div>

                    {{-- 本文編集フォーム（初期は非表示） --}}
                    <form method="POST"
                            action="{{ route('chat.update', $m) }}"
                            class="edit-form edit-form-{{ $m->id }}"
                            hidden>
                        @csrf @method('PATCH')

                        <div class="edit-row">
                        <textarea name="body" rows="1" class="composer-input" placeholder="メッセージを編集">{{ old('body', $m->body) }}</textarea>
                        </div>

                        <div class="edit-row">
                        <button type="submit" class="op-link btn-update">更新</button>
                        <button type="button" class="op-link js-edit-cancel" data-id="{{ $m->id }}">キャンセル</button>
                        </div>
                    </form>
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

{{-- チャット --}}
@section('js')
<script>
  (function(){
    // 既存：チャットを最下部へ
    const area = document.querySelector('.chat-area');
    if (area) area.scrollTop = area.scrollHeight;

    // 追加：編集フォームの開閉
    document.addEventListener('click', function(e){
      const openBtn = e.target.closest('.js-edit-toggle');
      if (openBtn) {
        const form = document.querySelector('.edit-form-' + openBtn.dataset.id);
        if (form) form.hidden = !form.hidden;
      }
      const cancelBtn = e.target.closest('.js-edit-cancel');
      if (cancelBtn) {
        const form = document.querySelector('.edit-form-' + cancelBtn.dataset.id);
        if (form) form.hidden = true;
      }
    }, false);
  })();
</script>
@endsection


{{-- 評価モーダル（自分が未評価のときだけ表示） --}}
@php
  $myID = auth()->id();
  $iAmBuyer  = $trade->buyer_id === $myID;
  $iAmSeller = $trade->seller_id === $myID;
  $iRated    = \App\Models\TradeRating::where('trade_id',$trade->id)->where('rater_id',$myID)->exists();

  $shouldOpen = false;
  // 購入者：完了直後 or buyer_completedで未評価
  if ($iAmBuyer && !$iRated && (session('completed') || $trade->status === 'buyer_completed')) {
      $shouldOpen = true;
  }
  // 出品者：buyer_completedで未評価
  if ($iAmSeller && !$iRated && $trade->status === 'buyer_completed') {
      $shouldOpen = true;
  }
  // ← 開発中だけURLで強制表示（採点に必要ならお使いください）
  //$force = app()->environment('local') && request()->boolean('forceModal');
  //$shouldOpen = $shouldOpen || $force;
@endphp

@if($shouldOpen)
<div class="modal-backdrop">
  <div class="modal">
    <div class="rating">取引が完了しました。</div>
    <div class="line"></div>
    <div class="question">今回の取引相手はどうでしたか？</div>
    <form method="POST" action="{{ route('trade.ratings.store', $trade) }}">
      @csrf
        <div class="stars">
            @for ($i = 5; $i >= 1; $i--)
                <input type="radio" id="star{{ $i }}" name="score" value="{{ $i }}">
                <label for="star{{ $i }}"></label>
            @endfor
        </div>
        <div class="line"></div>
        <div class="actions">
            <button type="submit" class="btn primary">送信する</button>
        </div>
    </form>
  </div>
</div>
@endif
@endsection
