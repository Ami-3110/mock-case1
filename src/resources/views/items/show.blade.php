@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/show.css') }}">
@endsection

@section('content')
<div class="item-container">
    <div class="item-information">
        <div class="item-wrapper">
            <div class="left-column">
                <img src="{{ asset('storage/' . $item->product_image) }}" alt="商品画像" class="product-image">
            </div>

            <div class="right-column">
                {{-- 商品名・ブランド・値段 --}}
                <h1 class="product_name">{{ $item->product_name }}</h1>
                <div class="brand">{{ $item->brand }}</div>
                <div class="price">¥{{ number_format($item->price) }}<p class="price-addition">（税込）</p></div>

                <div class="icon-group">
                    {{-- いいね --}}
                    <div class="icon-block">
                        <button id="like-button" data-product-id="{{ $item->id }}" class="icon-button" aria-pressed="{{ $isLiked ? 'true' : 'false' }}" aria-label="{{ $isLiked ? 'いいねを解除' : 'いいねする' }}">
                            <img id="like-icon" src="{{ asset($isLiked ? 'images/like-active.png' : 'images/like.png') }}" alt="いいねアイコン" class="icon-img">
                        </button>
                        <span id="like-count" class="icon-count">{{ $item->likes_count ?? $item->likes->count() }}</span>
                    </div>
                
                    {{-- コメント --}}
                    <div class="icon-block">
                        <img src="{{ asset('images/comment.png') }}" alt="コメントアイコン" class="icon-img">
                        <span id="comment-count" class="icon-count">{{ $item->comments_count ?? 0 }}</span>
                    </div>
                </div>
                <div></div>
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
                    <div class="item-attribute-row">
                        <div class="item-attribute">カテゴリー</div>
                        <div class="item-categories">
                            @foreach ($item->categories as $category)
                                <span class="category-tag">{{ $category->category_name }}</span>
                            @endforeach
                        </div>
                    </div>
                    {{-- 状態 --}}
                    <div class="item-attribute-row">
                        <div class="item-attribute">商品の状態</div>
                            <p class="condition">{{ $item->condition }}</p>
                    </div>

                {{-- コメント一覧 --}}
                <div class="item-heading_comment">コメント（{{ $item->comments_count ?? 0 }}）</div>
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
    </div>
</div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const likeButton = document.getElementById('like-button');
        const likeIcon = document.getElementById('like-icon');
        const likeCount = document.getElementById('like-count');
    
        likeButton.addEventListener('click', function() {
            const productId = likeButton.dataset.productId;
            const isLiked = likeButton.getAttribute('aria-pressed') === 'true';
            const url = `/like/${productId}`;
            const method = isLiked ? 'DELETE' : 'POST';
    
            fetch(url, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: method === 'POST' ? JSON.stringify({}) : null,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    likeButton.setAttribute('aria-pressed', (!isLiked).toString());
                    likeIcon.src = !isLiked ? '{{ asset('images/like-active.png') }}' : '{{ asset('images/like.png') }}';
                    likeCount.textContent = data.likes_count;
                } else {
                    alert('いいね操作に失敗しました');
                }
            })
            .catch(() => {
                alert('通信エラーが発生しました');
            });
        });
    });
</script> 
@endsection
    