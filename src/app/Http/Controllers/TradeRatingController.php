<?php

// app/Http/Controllers/TradeRatingController.php
namespace App\Http\Controllers;

use App\Http\Requests\TradeRatingRequest;
use App\Models\Trade;
use App\Models\TradeRating;

class TradeRatingController extends Controller
{
    public function store(TradeRatingRequest $request, Trade $trade)
    {
        $user = auth()->user();
        if ($trade->buyer_id !== $user->id && $trade->seller_id !== $user->id) abort(403);

        // 既に自分が評価済みならスキップ（連打対策）
        if (TradeRating::where('trade_id',$trade->id)->where('rater_id',$user->id)->exists()) {
            return redirect()->route('items.index');
        }

        $rateeId = $user->id === $trade->buyer_id ? $trade->seller_id : $trade->buyer_id;

        TradeRating::create([
            'trade_id' => $trade->id,
            'rater_id' => $user->id,
            'ratee_id' => $rateeId,
            'score'    => (int)$request->input('score'),
        ]);

        // 双方評価済みなら completed へ
        $buyerRated  = TradeRating::where('trade_id',$trade->id)->where('rater_id',$trade->buyer_id)->exists();
        $sellerRated = TradeRating::where('trade_id',$trade->id)->where('rater_id',$trade->seller_id)->exists();
        if ($buyerRated && $sellerRated) {
            $trade->update(['status' => 'completed']);
        }

        // 評価送信後は商品一覧へ（FN014）
        return redirect()->route('items.index')->with('rated', true);
    }
}
