@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/form.css') }}">
@endsection

@section('content')
<div class="purchase-form-container">
    {{-- 左2/3 --}}
    <div class="form-left">
        <div class="product-summary">
            <div class="product-top">
                <img src="{{ asset('storage/' . $item->product_image) }}" alt="{{ $item->product_name }}" class="product-image">
                <div class="product-details">
                    <h3 class="product-name">{{ $item->product_name }}</h3>
                    <p class="price">¥{{ number_format($item->price) }}</p>
                </div>
            </div>
        </div>
        

        <hr>

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

        <hr>

        {{-- 配送先 --}}
        <div class="form-group">
            <div class="form-label-with-link">
                <label for="ship_address">配送先</label>
                <a href="{{ route('purchase.updateAddress', ['item_id' => $item->id]) }}">変更する</a>
            </div>

            <p class="shipping-info">{{ $shipping['ship_postal_code'] }}</p>
            <p class="shipping-info">{{ $shipping['ship_address'] }}</p>
            <p class="shipping-info">{{ $shipping['ship_building'] }}</p>
        </div>

        <hr>
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
                    <span id="selected-payment_method"></span>
                </td>
            </tr>
        </table>

        <button type="submit" class="btn-submit">購入する</button>
    </form>
</div>

{{-- 選択中の支払い方法を即時表示 --}}
<script>
    const select = document.getElementById('payment_method');
    const display = document.getElementById('selected-payment_method');

    select.addEventListener('change', () => {
        if (select.value === "") {
            display.textContent = ""; // 
        } else {
            display.textContent = select.value;
        }
    });

    if (select.value !== "") {
        display.textContent = select.value;
    }

</script>
@endsection

