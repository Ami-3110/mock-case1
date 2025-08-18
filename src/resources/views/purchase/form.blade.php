@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/form.css') }}">
@endsection

@section('content')
{{-- Stripe経由の購入にしてありますのでコメントアウトしています。
<form method="POST" action="{{ route('purchase', ['item_id' => $item->id]) }}">
    @csrf
     --}}
<div class="purchase-form-container">
    {{-- 左2/3 --}}
    <div class="form-left">
        <div class="product-summary">
            <div class="product-top">
                <img src="{{ Storage::disk('public')->url($item->product_image) }}" alt="{{ $item->product_name }}" class="product-image">
                <div class="product-details">
                    <h3 class="product-name">{{ $item->product_name }}</h3>
                    <p class="price">¥{{ number_format($item->price) }}</p>
                </div>
            </div>
        </div>
        <hr class="hr-line">
    {{-- 支払い方法 --}}
        <div class="form-group">
            <label for="payment_method">支払い方法</label>
            <select  class="selectbox" name="payment_method" id="payment_method">
                <option value="">選択してください</option>
                <option value="コンビニ払い" {{ old('payment_method') === 'コンビニ払い' ? 'selected' : '' }}>コンビニ払い</option>
                <option value="カード支払い" {{ old('payment_method') === 'カード支払い' ? 'selected' : '' }}>カード支払い</option>
            </select>
            @error('payment_method')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>
        <hr class="hr-line">
    {{-- 配送先 --}}
        <div class="form-group">
            <div class="form-label-with-link">
                <label for="ship_address">配送先</label>
                <a href="{{ route('purchase.editAddressForm', ['item_id' => $item->id]) }}">変更する</a>
            </div>
            @if(isset($shipping))
                <p class="shipping-info">{{ $shipping['ship_postal_code'] }}</p>
                <p class="shipping-info">{{ $shipping['ship_address'] }}</p>
                <p class="shipping-info">{{ $shipping['ship_building'] }}</p>
            @else
                <p class="error-message">配送先が未設定です</p>
            @endif
        </div>
        <hr class="hr-line">
    </div>
{{-- 右1/3 --}}
    <div class="form-right">
        <table class="summary-table">
            <tr>
            <th>商品代金</th>
            <td>¥{{ number_format($item->price) }}</td>
            </tr>
            <tr>
            <th>支払い方法</th>
            <td>
                <span id="selected-payment_method">{{ old('payment_method') }}</span>
            </td>
            </tr>
        </table>
    {{-- ★ base URL を data 属性で持たせる --}}
        <div class="btn-wrapper">
            <form method="POST" action="{{ route('purchase.confirm', ['item_id' => $item->id]) }}">
                @csrf
                <input type="hidden" name="payment_method" id="payment_method_hidden" value="{{ old('payment_method') }}">
                <button type="submit" class="btn-submit">購入する</button>
            </form>
        </div>
    </div>
{{--</form>--}}

{{-- 選択中の支払い方法を即時表示 --}}
<script>
  const select  = document.getElementById('payment_method'); // 左のselect(既存)
  const display = document.getElementById('selected-payment_method');
  const hidden  = document.getElementById('payment_method_hidden');

  function syncUI() {
    const val = select.value || '';
    display.textContent = val; // 画面表示
    hidden.value = val;        // ★ POST用hiddenに同期（バリデ対象）
  }
  select.addEventListener('change', syncUI);
  syncUI();
</script>
@endsection

