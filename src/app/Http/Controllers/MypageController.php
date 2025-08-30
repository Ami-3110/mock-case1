<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Trade;
use App\Models\TradeRead;
use App\Models\TradeMessage;
use App\Http\Requests\AddressRequest;
use Illuminate\Support\Facades\Storage;

class MypageController extends Controller
{
    // マイページ画面
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $tab = $request->string('tab')->lower()->value() ?: 'sell';

        $sellItems     = collect();
        $buyItems      = collect();
        $tradingItems  = collect();
        $unreadCounts  = [];
        $totalUnread   = 0;

        // -------------------------------------
        // 1) タブごとのデータ本体
        // -------------------------------------
        if ($tab === 'sell') {
            $sellItems = Product::where('user_id', $user->id)
                ->withExists(['trades as has_active_trade' => function ($q) {
                    $q->whereIn('status', ['trading','buyer_completed','completed']);
                }])
                ->latest()
                ->get();
        }

        if ($tab === 'buy') {
            $buyItems = Purchase::with('product')
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        }

        if ($tab === 'trading') {
            $tradingItems = Trade::with([
                    'product:id,product_name,product_image,price',
                    'buyer:id,user_name',
                    'seller:id,user_name',
                ])
                ->where('status', 'trading')
                ->where(fn($q) => $q->where('buyer_id', $user->id)
                                    ->orWhere('seller_id', $user->id))
                ->orderByDesc('updated_at')
                ->get();
        }

        $tradesForUnread = $tab === 'trading'
            ? $tradingItems->pluck('id')
            : Trade::where('status', 'trading')
                ->where(fn($q) => $q->where('buyer_id', $user->id)
                                    ->orWhere('seller_id', $user->id))
                ->pluck('id');

        if ($tradesForUnread->isNotEmpty()) {
            $reads = TradeRead::whereIn('trade_id', $tradesForUnread)
                ->where('user_id', $user->id)
                ->get()
                ->keyBy('trade_id');

            foreach ($tradesForUnread as $tradeId) {
                $lastReadId = $reads->get($tradeId)?->last_read_message_id;

                $cnt = TradeMessage::where('trade_id', $tradeId)
                    ->when($lastReadId, fn($q) => $q->where('id', '>', $lastReadId))
                    ->count();

                if ($tab === 'trading') {
                    $unreadCounts[$tradeId] = $cnt;
                }

                $totalUnread += $cnt;
            }
        }

        $ratingAvg = \App\Models\TradeRating::where('ratee_id', $user->id)->avg('score');
        $ratingAvg = $ratingAvg ? round($ratingAvg, 1) : 0; 
        $filledStars = (int) round($ratingAvg);

        // ビュー返却（どのタブでも未読総数が埋まっている）
        return view('mypage.index', compact(
            'user',
            'ratingAvg',
            'filledStars',
            'tab',
            'sellItems',
            'buyItems',
            'tradingItems',
            'unreadCounts',
            'totalUnread'
        ));
    }



    // プロフィール編集画面
    public function edit(){
        $user = auth()->user()->load('userProfile');
        return view('mypage.edit', compact('user'));
    }

    // プロフィール更新処理
    public function updateProfile(AddressRequest $request){
        $user = auth()->user();

        $profile = $user->userProfile ?? new \App\Models\UserProfile(['user_id' => $user->id]);

        if ($request->hasFile('user_image')) {
            if ($profile->profile_image && Storage::disk('public')->exists($profile->profile_image)) {
                Storage::disk('public')->delete($profile->profile_image);
            }

            $originalName = $user->id . '_' . $request->file('user_image')->getClientOriginalName();
            $path = $request->file('user_image')->storeAs('user_images', $originalName, 'public');

            $profile->profile_image = $path;
        }

        $profile->postal_code = $request->postal_code;
        $profile->address = $request->address;
        $profile->building = $request->building;
        $profile->save();

        $user->user_name = $request->user_name;
        $user->save();

        return redirect('/mypage');
    }
    
}
