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
                <img src="{{ asset('storage/' . $user->userProfile->profile_image) }}" alt="プロフィール画像" class="profile-image">
            @else
                <div class="profile-placeholder"></div>
            @endif
        </div>
        
        
        <div class="profile-info">
            <div class="profile-left">
                <h2 class="username">{{ $user->user_name }}</h2>
            </div>
            <div class="profile-right">
                <a href="{{route('mypage.edit') }}" class="edit-profile-btn">プロフィールを編集</a>
            </div>
        </div>
    </div>

    {{-- タブメニュー --}}
    <div class="tabs">
        <a href="{{ route('mypage.index', ['tab' => 'sell']) }}" class="{{ $tab === 'sell' ? 'tab active' : 'tab' }}">出品した商品</a>
        <a href="{{ route('mypage.index', ['tab' => 'buy']) }}" class="{{ $tab === 'buy' ? 'tab active' : 'tab' }}">購入した商品</a>
    </div>
    <div class="tab-border"></div>

    {{-- 商品一覧 --}}
    <div class="product-list">
        @if ($tab === 'sell')
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
        @elseif ($tab === 'buy')
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
                            </div>
                            @endif
                            
                        </div>
                    @endif
                @endforeach
            @endif
        @endif
    </div>
    
</div>
@endsection
