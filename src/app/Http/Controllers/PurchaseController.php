<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\UserProfile;
use App\Models\Comment;
use App\Models\Like;
use App\Models\User;

class PurchaseController extends Controller
{
// 購入フォーム表示
    public function showForm($item_id){
        $item = Product::findOrFail($item_id);
        $user = auth()->user();
        $userProfile = $user->userProfile;

        // セッションに住所があるかチェック
        $sessionKey = 'shipping_address_' . $item_id;
        $shipping = session($sessionKey, [
            'ship_postal_code' => $userProfile->postal_code,
            'ship_address'     => $userProfile->address,
            'ship_building'    => $userProfile->building,
        ]);

        return view('purchase.form', compact('item', 'shipping'));
    }



// 購入処理
    public function purchase(Request $request, $item_id)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string|in:コンビニ払い,カード支払い',
        ]);

        $shipping = session('shipping_address_' . $item_id);

        if (!$shipping) {
            return redirect()->back()->withErrors(['配送先住所が見つかりません。もう一度入力してください。']);
        }

        Purchase::create([
            'user_id'          => auth()->id(),
            'product_id'       => $item_id,
            'payment_method'   => $validated['payment_method'],
            'ship_postal_code' => $shipping['ship_postal_code'],
            'ship_address'     => $shipping['ship_address'],
            'ship_building'    => $shipping['ship_building'],
            'purchased_at'     => now(),
        ]);

        // 商品の is_sold を更新
        $item = Product::findOrFail($item_id);
        $item->is_sold = true;
        $item->save();

        // セッションから配送先を削除
        session()->forget('shipping_address_' . $item_id);

        // 購入完了 → マイページ（購入タブ）へリダイレクト
        return redirect()->route('mypage.index', ['tab' => 'buy']);
    }


// 住所変更画面の表示(GET)
    public function editAddressForm($item_id){
        $item = Product::findOrFail($item_id);
        $user = auth()->user();
        $userProfile = $user->userProfile;

        $purchase = Purchase::where('user_id', $user->id)
                    ->where('product_id', $item->id)
                    ->latest()
                    ->first();

        $shipping = [
            'postal_code' => $purchase?->ship_postal_code ?? $userProfile->postal_code,
            'address'     => $purchase?->ship_address     ?? $userProfile->address,
            'building'    => $purchase?->ship_building    ?? $userProfile->building,
        ];

        return view('purchase.address', compact('item', 'shipping'));
    }


    // 住所変更処理(POST)
    public function updateAddress(Request $request, $item_id){
        $validated = $request->validate([
            'ship_postal_code' => 'required|string|max:10',
            'ship_address'     => 'required|string|max:255',
            'ship_building'    => 'nullable|string|max:255',
        ]);

        // セッションに保存
        session([
            'shipping_address_' . $item_id => $validated
        ]);

        return redirect()->route('purchase.showForm', ['item_id' => $item_id]);
    }




}
