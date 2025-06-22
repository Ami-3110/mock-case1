@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/items.css') }}">
@endsection

@section('content')
<div class="tab-container">
    <div class="tabs">
        <a href="{{ route('items.index', ['tab' => 'recommend']) }}"
            class="{{ request()->tab === 'recommend' || request()->tab === null ? 'tab active' : 'tab' }}">おすすめ</a>         
        <a href="{{ route('items.index', ['tab' => 'mylist']) }}" class="{{ request()->tab === 'mylist' ? 'tab active' : 'tab' }}">マイリスト</a>
    </div>

    <div class="tab-border"></div>
    
    <div class="tab-content">
        @if ($activeTab === 'recommend')
        @elseif ($activeTab === 'mylist')
        @endif
    </div>
    
    <div class="product-list">
        @foreach ($products as $item)
            @if ($activeTab === 'mylist')
                @php
                    $product = $item->product; // Like → Product
                @endphp
            @else
                @php
                    $product = $item; // すでに Product
                @endphp
            @endif
    
            @if ($product) {{-- 念のためnullチェック --}}
            <div class="product-card">
                <a href="{{ route('item.show', ['item_id' => $product->id]) }}">
                    <img src="{{ asset('storage/' . $product->product_image) }}" alt="商品画像" class="product-image">
            
                    <div class="product-info-row">
                        <p class="product-name">{{ $product->product_name }}</p>
            
                        @if ($product->is_sold)
                            <span class="sold-label">sold</span>
                        @endif
                    </div>
                </a>
            </div>
            
            @endif
        @endforeach
    </div>
    
    
</div>
@endsection