@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/edit.css') }}">
@endsection

@section('content')
<div class="profile-edit-container">
    <h2>プロフィール設定</h2>

    <form method="POST" action="{{ route('mypage.updateProfile') }}" enctype="multipart/form-data">
        @csrf

         {{-- ユーザー名 --}}
        <div class="form-group">
            <label for="user_name">ユーザー名</label>
            <input type="text" name="user_name" id="user_name" value="{{ old('user_name', $user->user_name) }}" class="form-input">
            @error('user_name')<div class="error">{{ $message }}</div>@enderror
        </div>

        {{-- プロフィール画像 --}}
        <div class="form-group">
            <label for="profile_image">プロフィール画像</label>

            @if ($user->userProfile && $user->userProfile->profile_image)
                <div>
                    {{-- 確実に正しいパスで表示 --}}
                    <img src="{{ asset('storage/' . $user->userProfile->profile_image) }}?v={{ time() }}" alt="プロフィール画像" width="100" style="border: 1px solid red;">
                </div>
            @else
                <div class="default-image-circle">No Image</div>
            @endif

            <input type="file" name="user_image" id="profile_image" class="form-input" accept="image/*">
            @error('profile_image')<div class="error">{{ $message }}</div>@enderror
        </div>


        {{-- 郵便番号 --}}
        <div class="form-group">
            <label for="postal_code">郵便番号</label>
            <input type="text" name="postal_code" id="postal_code" class="form-input" value="{{ old('postal_code', $user->userProfile->postal_code ?? '') }}">
            @error('postal_code')<div class="error">{{ $message }}</div>@enderror
        </div>

        {{-- 住所 --}}
        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" name="address" id="address" value="{{ old('address', $user->userProfile->address ?? '') }}" class="form-input">
            @error('address')<div class="error">{{ $message }}</div>@enderror
        </div>

        {{-- 建物名 --}}
        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" name="building" id="building" value="{{ old('building', $user->userProfile->building ?? '') }}" class="form-input">
            @error('building')<div class="error">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="edit-button">更新する</button>
    </form>
</div>
@endsection
