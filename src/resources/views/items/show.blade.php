@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection

@section('content')
<div class="item-container">
    <div class="item-information">
        <img src="{{ asset('storage/' . $item->product_image) }}" alt="商品画像" class="product-image">

        {{-- 商品名・ブランド・値段 --}}
        <h1 class="product_name">{{ $item->name }}</h1>
        <div class="brand">{{ $item->brand }}
        <div class="price">¥{{ number_format($item->price) }}<p class="price-addition">（税込）</p></div>

        {{-- いいね機能／コメント数 --}}
        <div class="like-comment-area">
            <div class="like-mark">
                
                <form action="{{ $isLiked ? route('like.destroy', ['product' => $item->id]) : route('like.store', ['product' => $item->id]) }}" method="POST">
                    @csrf
                    @if ($isLiked)
                        @method('DELETE')
                    @endif
                    <button type="submit" aria-label="{{ $isLiked ? 'いいねを解除' : 'いいねする' }}">
                        {{ $likeSymbol }}
                    </button>
                </form>


                <span class="likes-count">{{ $item->likes_count ?? $item->likes->count() }}</span>
            </div>
        </div>

            <span class="comment-mark">コメントマーク{{ $item->comments_count ?? 0 }}</span>
        </div>

        {{-- 購入ボタン --}}
        <a href="{{ route('purchase.showForm', ['item_id' => $item->id]) }}"
           class="purchase-btn">
            購入手続きへ
        </a>
    
        {{-- 商品説明 --}}
        <div class="item-heading">商品説明</div>
        <p class="description">{{ $item->description }}</p>

        {{-- 商品情報 --}}
        <div class="item-heading">商品の情報</div>
            {{-- カテゴリー --}}
            <div class="item-attribute">カテゴリー
                <p class="item-categories">
                    @foreach ($item->categories as $category)
                        {{ $category->category_name }}{{ !$loop->last ? ' / ' : '' }}
                    @endforeach
                </p>
            </div>
            {{-- 状態 --}}
            <div class="item-attribute">商品の状態
                <p class="condition">{{ $item->condition }}</p>
            </div>

        {{-- コメント一覧 --}}
        <div class="item-heading">コメント（{{ $item->comments_count ?? 0 }}）</div>
        @forelse ($item->comments as $comment)
            <div class="comment-user">
                @if ($comment->user->userProfile && $comment->user->userProfile->profile_image)
                    <img src="{{ asset('storage/' . $comment->user->userProfile->profile_image) }}" alt="プロフィール画像" class="comment-user-image">
                @else
                    <div class="default-comment-image"></div>
                @endif
                <p class="comment-name">{{ $comment->user->user_name }}</p>
            </div>
            <div class="comment-content">{{ $comment->comment }}</div>
        @empty
            <div class="no-comments-yet">まだコメントはありません。</div>
        @endforelse

        {{-- コメント投稿フォーム --}}
        <div class="item-attribute">商品へのコメント</div>
        <form action="{{ route('comments.store', ['item' => $item->id]) }}" method="POST">
            @csrf
            <textarea name="comment" class="comment-form" placeholder="">{{ old('comment') }}</textarea>
            @error('comment')
                <p class="comment_error">{{ $message }}</p>
            @enderror
            <button type="submit" class="comment-btn">コメントを送信する</button>
        </form>        
        </div>
    </div>
</div>
@endsection
