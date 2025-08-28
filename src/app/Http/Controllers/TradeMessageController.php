<?php

// app/Http/Controllers/TradeMessageController.php
namespace App\Http\Controllers;

use App\Http\Requests\TradeMessageRequest;
use App\Models\Trade;
use App\Models\TradeMessage;
use App\Models\TradeRead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TradeMessageController extends Controller
{
    // 投稿（US003: FN006/007/008/009）
    public function store(TradeMessageRequest $request, Trade $trade)
    {
        $user = auth()->user();

        // 当事者チェック
        if ($trade->buyer_id !== $user->id && $trade->seller_id !== $user->id) {
            abort(403);
        }

        // 画像保存（public disk）
        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('trade_messages', 'public');
        }

        TradeMessage::create([
            'trade_id'   => $trade->id,
            'user_id'    => $user->id,
            'body'       => $request->input('body'),
            'image_path' => $path,
        ]);

        // 並び替え用に updated_at をタッチ（FN004）
        $trade->touch();

        // 入力欄は送信成功時は空でOK。戻るだけ
        return back();
    }

    // 編集（US004: FN010）
    public function update(TradeMessageRequest $request, TradeMessage $message)
    {
        $user = auth()->user();
        if ($message->user_id !== $user->id) abort(403);

        // 本文のみ必須仕様なので、画像差替えは任意。最小実装は本文更新のみ。
        $message->update(['body' => $request->input('body')]);

        // 紐づく取引の updated_at を更新
        $message->trade->touch();

        return back();
    }

    // 削除（US004: FN011）
    public function destroy(TradeMessage $message)
    {
        $user = auth()->user();
        if ($message->user_id !== $user->id) abort(403);

        // 画像があれば削除
        if ($message->image_path) {
            Storage::disk('public')->delete($message->image_path);
        }

        $trade = $message->trade;
        $message->delete();

        $trade->touch();
        return back();
    }
}
