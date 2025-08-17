<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Product;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function store(Request $request, $item_id)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false], 401);
        }

        $item = Product::findOrFail($item_id);

        Like::firstOrCreate([
            'user_id'    => auth()->id(),
            'product_id' => $item->id,
        ]);

        $count = Like::where('product_id', $item->id)->count();

        return response()->json([
            'success'     => true,
            'likes_count' => $count,
        ]);
    }

    public function destroy(Request $request, $item_id)
    {
        if (!auth()->check()) {
            return response()->json(['success' => false], 401);
        }

        $item = Product::findOrFail($item_id);

        Like::where('user_id', auth()->id())
            ->where('product_id', $item->id)
            ->delete();

        $count = Like::where('product_id', $item->id)->count();

        return response()->json([
            'success'     => true,
            'likes_count' => $count,
        ]);
    }
}
