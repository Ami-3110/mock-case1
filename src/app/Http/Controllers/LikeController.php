<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Like;
use App\Models\Product;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function store(Request $request, $item_id){
        if (auth()->check()) {
        // ログインユーザーの処理
            Like::firstOrCreate([
                'user_id' => auth()->id(),
                'product_id' => $item_id,
            ]);
            return redirect()->route('item.show', ['item_id' => $item_id]);
        }
        return redirect()->route('login');
    }

    public function destroy(Request $request, $item_id){
        if (auth()->check()) {
            Like::where('user_id', auth()->id())
                ->where('product_id', $item_id)
                ->delete();

            return redirect()->route('item.show', ['item_id' => $item_id]);
        }

        // 未ログインならログインページへリダイレクト
        return redirect()->route('login');
    }


}
