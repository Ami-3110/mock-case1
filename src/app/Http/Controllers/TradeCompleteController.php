<?php
// app/Http/Controllers/TradeCompleteController.php
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
        // 購入者のみ完了可能
        if ($trade->buyer_id !== $user->id) { abort(403); }

        // 状態遷移（trading -> buyer_completed または completed）
        if ($trade->status === 'trading') {
            $trade->update(['status' => 'buyer_completed']);
        }

        // 出品者にメール通知（FN016）
        Mail::to($trade->seller->email)->send(new TradeCompletedMail($trade));

        // この時点で購入者の評価モーダルを出すのが要件（FN012）
        // → Blade 側で $role==='buyer' && 未評価ならモーダル開く

        return back()->with('completed', true);
    }
}
