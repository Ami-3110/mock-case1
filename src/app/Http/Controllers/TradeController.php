<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use App\Models\TradeMessage;
use App\Models\TradeRead;
use Illuminate\Http\Request;

class TradeController extends Controller
{
    public function show(Trade $trade)
    {
        $user = auth()->user();

        if ($trade->buyer_id !== $user->id && $trade->seller_id !== $user->id) {
            abort(403);
        }

        $role = $trade->buyer_id === $user->id ? 'buyer' : 'seller';

        $messages = TradeMessage::with('user')
            ->where('trade_id', $trade->id)
            ->orderBy('id')
            ->get();

        $sideTrades = Trade::with('product')
            ->where('status','trading')
            ->where(fn($q)=>$q->where('buyer_id',$user->id)->orWhere('seller_id',$user->id))
            ->orderByDesc('updated_at')
            ->get();

        $canComplete = ($role === 'buyer');

        if ($messages->isNotEmpty()) {
            TradeRead::updateOrCreate(
                ['trade_id' => $trade->id, 'user_id' => $user->id],
                ['last_read_message_id' => $messages->last()->id]
            );
        }

        return view('trades.show', compact('trade','messages','sideTrades','role','canComplete'));
    }
}
