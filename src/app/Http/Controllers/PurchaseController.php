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
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Support\Facades\App;

class PurchaseController extends Controller
{
// 購入フォーム表示
    public function showForm($item_id){
    $item = Product::findOrFail($item_id);
    $user = auth()->user();

    $defaultShipping = null;
    if ($user && $user->userProfile) {
        $defaultShipping = [
            'ship_postal_code' => $user->userProfile->postal_code,
            'ship_address'     => $user->userProfile->address,
            'ship_building'    => $user->userProfile->building,
        ];
    }

    $shipping = session('shipping_address_' . $item_id);
    if (!$shipping && $defaultShipping) {
        session(['shipping_address_' . $item_id => $defaultShipping]);
        $shipping = $defaultShipping;
    }

    return view('purchase.form', compact('item', 'shipping'));
    }

// 購入処理（Stripeに切り替えてあります）
//  public function purchase(PurchaseRequest $request, $item_id){
//        $validated = $request->validate([
//        'payment_method' => 'required|string|in:コンビニ払い,カード支払い',
//        ]);
//
//        $shipping = session('shipping_address_' . $item_id);
//
//        if (!$shipping) {
//        return redirect()->back()->withErrors(['配送先住所が見つかりません。もう一度入力してください。']);
//        }
//
//        Purchase::create([
//        'user_id'          => auth()->id(),
//        'product_id'       => $item_id,
//        'payment_method'   => $validated['payment_method'],
//        'ship_postal_code' => $shipping['ship_postal_code'],
//        'ship_address'     => $shipping['ship_address'],
//        'ship_building'    => $shipping['ship_building'],
//        'purchased_at'     => now(),
//        ]);
//
//        // 商品の is_sold を更新
//        $item = Product::findOrFail($item_id);
//        $item->is_sold = true;
//        $item->save();
//
//        // セッションから配送先を削除
//        session()->forget('shipping_address_' . $item_id);
//
//        // 購入完了 → サンクスページへリダイレクト
//        return redirect()->route('purchase.thanks');
//    }

// 購入御礼ページ表示
    public function thanks(){
        return view('purchase.thanks');
    }

// 住所変更画面の表示
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

// 住所変更処理
    public function updateAddress(PurchaseRequest $request, $item_id){
        $validated = $request->validated();
        session([
            'shipping_address_' . $item_id => $validated
        ]);

        return redirect()->route('purchase.showForm', ['item_id' => $item_id]);
    }

// Stripe
    public function redirectToStripe(Request $request, $item_id){
        $item = Product::findOrFail($item_id);

        $shipping = session('shipping_address_' . $item_id);

        if (!$shipping) {
            return redirect()->back()->withErrors(['配送先住所が見つかりません。もう一度入力してください。']);
        }

        Purchase::create([
            'user_id'          => auth()->id(),
            'product_id'       => $item_id,
            'payment_method'   => 'カード支払い',
            'ship_postal_code' => $shipping['ship_postal_code'],
            'ship_address'     => $shipping['ship_address'],
            'ship_building'    => $shipping['ship_building'],
            'purchased_at'     => now(),
        ]);

        $item->is_sold = true;
        $result = $item->save();
        if (app()->environment('local', 'testing')) {
        \Log::debug('is_sold更新成功？: ' . var_export($result, true));
        }

        session()->forget('shipping_address_' . $item_id);

        if (!App::environment('testing')) {
            Stripe::setApiKey(config('services.stripe.secret'));

            \Log::debug('Stripe success URL: ' . route('purchase.thanks'));

            $checkout = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'unit_amount' => $item->price,
                        'product_data' => [
                            'name' => $item->product_name,
                        ],
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('purchase.thanks'), 
                'cancel_url' => route('purchase.showForm', ['item_id' => $item_id]),
            ]);

            return redirect($checkout->url);
        }
        return redirect()->route('purchase.thanks');
    }
}
