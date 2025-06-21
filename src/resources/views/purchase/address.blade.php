@extends('layouts.app')
@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')


<div class="address-edit-container">
    <h2>配送先住所の変更</h2>

    <form action="{{ route('purchase.updateAddress', ['item_id' => $item->id]) }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="ship_postal_code">郵便番号</label>
            <input type="text" name="ship_postal_code" id="ship_postal_code" value="{{ old('ship_postal_code', $shipping['postal_code']) }}">
            @error('ship_postal_code')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="ship_address">住所</label>
            <input type="text" name="ship_address" id="ship_address" value="{{ old('ship_address', $shipping['address']) }}">
            @error('ship_address')
            <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="ship_building">建物名（任意）</label>
            <input type="text" name="ship_building" id="ship_building" value="{{ old('ship_building', $shipping['building']) }}">
        </div>

        <button type="submit" class="btn-submit">更新する</button>
    </form>
</div>
@endsection
