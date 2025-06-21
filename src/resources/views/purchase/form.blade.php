@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/form.css') }}">
@endsection

@section('content')
<div class="purchase-form-container">
    <div class="product-info">
        <img src="{{ asset('storage/' . $item->product_image) }}" alt="{{ $item->product_name }}" class="product-image">
        <h3>{{ $item->product_name }}</h3>
        <p>¥{{ number_format($item->price) }}</p>
    </div>

    <form action="{{ route('purchase.purchase', ['item_id' => $item->id]) }}" method="POST">
        @csrf

        {{-- 支払い方法 --}}
        <div class="form-group">
            <label for="payment-method">支払い方法</label>
            <select name="payment_method" id="payment-method" required>
                <option value="コンビニ払い" {{ old('payment_method') === 'コンビニ払い' ? 'selected' : '' }}>コンビニ払い</option>
                <option value="カード支払い" {{ old('payment_method') === 'カード支払い' ? 'selected' : '' }}>カード支払い</option>
            </select>
            @error('payment_method')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        {{-- 配送先 --}}
        <div class="form-group">
            <div class="form-label-with-link">
                <label for="postal_code">配送先</label>
                <a href="{{ route('purchase.updateAddress', ['item_id' => $item->id]) }}">変更する</a>
            </div>

            <p>{{ $shipping['ship_postal_code'] }}</p>
            <p>{{ $shipping['ship_address'] }}</p>
            <p>{{ $shipping['ship_building'] }}</p>
        </div>

        <table>
            <tr>
                <th>商品代金</th>
                <td>¥{{ number_format($item->price) }}</td>
            </tr>
            <tr>
                <th>支払い方法</th>
                <td>
                    {{-- 選択した支払い方法をJSなどで表示したい場合、ここに動的表示 --}}
                    <span id="selected-payment-method">未選択</span>
                </td>
            </tr>
        </table>

        <button type="submit" class="btn-submit">購入する</button>
    </form>
</div>

{{-- JavaScriptで選択中の支払い方法を表示（オプション） --}}
<script>
    const select = document.getElementById('payment-method');
    const display = document.getElementById('selected-payment-method');
    if (select && display) {
        display.textContent = select.value;
        select.addEventListener('change', () => {
            display.textContent = select.value;
        });
    }
</script>
@endsection

