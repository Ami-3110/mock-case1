<?php

namespace App\Http\Controllers;

use App\Models\Trade;
use App\Models\TradeRating;
use App\Mail\TradeCompletedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class TradeCompleteController extends Controller
{
    public function store(Request $request, Trade $trade)
    {
        $user = auth()->user();
        if ($trade->buyer_id !== $user->id) { abort(403); }

        if ($trade->status === 'trading') {
            $trade->update(['status' => 'buyer_completed']);
        }

        Mail::to($trade->seller->email)->send(new TradeCompletedMail($trade));

        return back()->with('completed', true);
    }
}
