<?php

namespace App\Http\Controllers;

use App\Http\Requests\TradeMessageRequest;
use App\Models\Trade;
use App\Models\TradeMessage;
use App\Models\TradeRead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TradeMessageController extends Controller
{
    // 投稿
    public function store(TradeMessageRequest $request, Trade $trade)
    {
        $user = auth()->user();

        if ($trade->buyer_id !== $user->id && $trade->seller_id !== $user->id) {
            abort(403);
        }

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

        $trade->touch();

        return back();
    }

    // 編集
    public function update(TradeMessageRequest $request, TradeMessage $message)
    {
        $user = auth()->user();
        if ($message->user_id !== $user->id) abort(403);

        $data = [
            'body' => $request->input('body'),
        ];

        if ($request->boolean('remove_image') && $message->image_path) {
            \Storage::disk('public')->delete($message->image_path);
            $data['image_path'] = null;
        }

        if ($request->hasFile('image')) {
            if ($message->image_path) {
                \Storage::disk('public')->delete($message->image_path);
            }
            $data['image_path'] = $request->file('image')->store('trade_messages', 'public');
        }

        $message->update($data);

        $message->trade->touch();

        return back();
    }

    // 削除
    public function destroy(TradeMessage $message)
    {
        $user = auth()->user();
        if ($message->user_id !== $user->id) abort(403);

        if ($message->image_path) {
            Storage::disk('public')->delete($message->image_path);
        }

        $trade = $message->trade;
        $message->delete();

        $trade->touch();
        return back();
    }
}
