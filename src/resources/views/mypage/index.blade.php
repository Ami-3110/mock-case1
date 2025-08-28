@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}">
@endsection

@section('content')
<div class="mypage-container">

{{-- 上段プロフィールエリア --}}
<div class="profile-header">
  <div class="profile-image-wrapper">
    @if ($user->userProfile && $user->userProfile->profile_image)
      <img
        src="{{ Storage::disk('public')->url($user->userProfile->profile_image ?? 'user_images/default.png') }}"
        alt="{{ $user->user_name }}"
        class="profile-image"
      />
    @endif
  </div>

  <div class="profile-info">
    {{-- 左：ユーザー名＋星（縦積み） --}}
    <div class="user-meta">
      <h2 class="username">{{ $user->user_name }}</h2>
      <div class="stars" aria-label="評価">
        @php
          $filledStars = isset($filledStars) ? (int)$filledStars : (int)($ratingAvgRounded ?? 0);
        @endphp
        @for ($i = 1; $i <= 5; $i++)
          <img
            src="{{ asset('images/' . ($i <= $filledStars ? 'Star1.png' : 'Star0.png')) }}"
            alt=""
            width="18" height="18"
          >
        @endfor
      </div>
    </div>

    {{-- 右：編集ボタン --}}
    <div class="profile-right">
      <a href="{{ route('mypage.edit') }}" class="edit-profile-btn">プロフィールを編集</a>
    </div>
  </div>
</div>

  {{-- タブメニュー（リンクだけ） --}}
<div class="tabs">
    <a href="?tab=sell"     class="tab {{ $tab === 'sell' ? 'active' : '' }}">出品した商品</a>
    <a href="?tab=buy"      class="tab {{ $tab === 'buy' ? 'active' : '' }}">購入した商品</a>
{{-- 取引中タブに総未読件数バッジ（0件のときは非表示） --}}
    <a href="?tab=trading"  class="tab {{ $tab === 'trading' ? 'active' : '' }}">  
    取引中の商品
        @if(($totalUnread ?? 0) > 0)
        <span class="tab-badge">{{ $totalUnread > 99 ? '99+' : $totalUnread }}</span>
        @endif
    </a>
</div>

  {{-- タブ下のボーダー --}}
  <div class="tab-border"></div>

  {{-- タブの中身 --}}
  <div class="tab-panel">
    @if ($tab === 'sell')
      <div class="product-list">
        @if ($sellItems->isEmpty())
          <p class="empty-message">まだ出品した商品はありません。</p>
        @else
          @foreach ($sellItems as $product)
            <div class="product-card">
              <a href="{{ route('item.show', ['item_id' => $product->id]) }}">
                <img src="{{ asset('storage/' . $product->product_image) }}" alt="商品画像" class="product-image">
              </a>
              <p class="product-name">{{ $product->product_name }}</p>
              @if ($product->is_sold)
                <span class="sold-label">sold</span>
              @endif
            </div>
          @endforeach
        @endif
      </div>

    @elseif ($tab === 'buy')
      <div class="product-list">
        @if ($buyItems->isEmpty())
          <p class="empty-message">まだ購入した商品はありません。</p>
        @else
          @foreach ($buyItems as $purchase)
            @if ($purchase->product)
              <div class="product-card">
                <a href="{{ route('item.show', ['item_id' => $purchase->product->id]) }}">
                  <img src="{{ asset('storage/' . $purchase->product->product_image) }}" alt="商品画像" class="product-image">
                </a>
                <div class="product-info-row">
                  <p class="product-name">{{ $purchase->product->product_name }}</p>
                  @if ($purchase->product->is_sold)
                    <span class="sold-label">sold</span>
                  @endif
                </div>
              </div>
            @endif
          @endforeach
        @endif
      </div>

    @elseif ($tab === 'trading')
        <div class="product-list trade-list">
            @forelse($tradingItems as $trade)
                @php $unread = ($unreadCounts[$trade->id] ?? 0); @endphp
                <div class="product-card has-badge" data-trade-id="{{ $trade->id }}">
                    @if($unread > 0)
                        <span class="badge-unread">{{ $unread > 99 ? '99+' : $unread }}</span>
                    @endif
                <div class="product-row">
                <a href="{{ route('trades.show', $trade) }}" class="btn-chat">
                    <img src="{{ asset('storage/' . $trade->product->product_image) }}" class="product-image" alt="商品画像">
                </a>
                <div class="product-info-row">
                    <div class="product-name">
                    {{ $trade->product->product_name }}
                    </div>
                </div>
                </div>
            </div>
            @empty
            <p class="empty-message">取引中の商品はありません。</p>
            @endforelse
        </div>
        @endif

  </div>
</div>
@endsection
