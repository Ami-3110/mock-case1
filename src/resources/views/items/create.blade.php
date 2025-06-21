@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/create.css') }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
@endsection

@section('content')
<div class="form-container">
    <h2 class="title">商品を出品</h2>

    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- 商品画像 --}}
        <div class="form-group">
            <label class="label" for="image">商品画像</label>
            <div class="image-space" style="position: relative;">
                <input class="image" type="file" id="product_image" name="product_image" accept="image/*" style="display:none;">
                <label for="product_image" class="custom-file-button">画像を選択する</label>
                <img class="image-preview" id="image-preview" src="{{ old('product_image') ? asset('storage/' . old('product_image')) : '#' }}" 
                alt="プレビュー" style="{{ old('product_image') ? '' : 'display:none;' }} max-height:100%; max-width:100%; object-fit: contain;">
                <button class="remove-image" type="button" id="remove-image" 
                style="{{ old('product_image') ? '' : 'display:none;' }} position:absolute; top:4px; right:4px;">×</button>
            </div>
        </div>

        <div class="product-detail">商品の詳細</div>

        {{-- カテゴリ --}}
        <div class="form-group">
            <label class="label">カテゴリー</label>
            <div class="category-tags">
                @foreach ($categories as $category)
                    <label class="checkbox-tag">
                        <input type="checkbox" class="hidden-checkbox" name="category_ids[]" value="{{ $category->id }}" {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}>
                        <span class="tag">{{ $category->category_name }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- 商品の状態 --}}
        <div class="form-group">
            <label class="label" for="condition">商品の状態</label>
            <select name="condition" id="condition">
                <option value="良好" {{ old('condition') === '良好' ? 'selected' : '' }}>良好</option>
                <option value="目立った傷や汚れなし" {{ old('condition') === '目立った傷や汚れなし' ? 'selected' : '' }}>目立った傷や汚れなし</option>
                <option value="やや傷や汚れあり" {{ old('condition') === 'やや傷や汚れあり' ? 'selected' : '' }}>やや傷や汚れあり</option>
                <option value="状態が悪い" {{ old('condition') === '状態が悪い' ? 'selected' : '' }}>状態が悪い</option>
            </select>
        </div>

        {{-- 商品名 --}}
        <div class="form-group">
            <label class="label" for="product_name">商品名</label>
            <input class="product_name" type="text" id="product_name" name="product_name" value="{{ old('product_name') }}">
        </div>

        {{-- ブランド名 --}}
        <div class="form-group">
            <label class="label" for="brand">ブランド名</label>
            <input class="brand" type="text" id="brand" name="brand" value="{{ old('brand') }}">
        </div>

        {{-- 商品説明 --}}
        <div class="form-group">
            <label class="label" for="description">説明</label>
            <textarea class="description" id="description" name="description" rows="4">{{ old('description') }}</textarea>
        </div>

        {{-- 価格 --}}
        <div class="form-group">
            <label class="label" for="price">販売価格</label>
            <input type="number" class="price" name="price" value="{{ old('price') }}">
        </div>

        <button type="submit" class="submit-button">出品する</button>
    </form>
</div>
@endsection
@section('js')
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('product_image');
        const preview = document.getElementById('image-preview');
        const removeBtn = document.getElementById('remove-image');
        const customFileButton = document.querySelector('label[for="product_image"]');

        // 画像プレビュー
        input.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    removeBtn.style.display = 'block';
                    customFileButton.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        removeBtn.addEventListener('click', function () {
            input.value = '';
            preview.src = '#';
            preview.style.display = 'none';
            this.style.display = 'none';
            removeBtn.style.display = 'none';
            customFileButton.classList.remove('hidden');
        });


        //状態のプルダウン
        const conditionElement = document.getElementById('condition');
        if (conditionElement) {
            new Choices(conditionElement, {
                placeholder: true,
                placeholderValue: '選択してください',
                searchEnabled: false,
                itemSelectText: '',
                shouldSort: false,
            });
        }
    });

</script>
@endsection
