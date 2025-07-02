<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Like;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LikeController extends Controller
{
    public function store(Request $request, $item_id){
        if (auth()->check()) {
            Like::firstOrCreate([
                'user_id' => auth()->id(),
                'product_id' => $item_id,
            ]);

            $count = Like::where('product_id', $item_id)->count();
            return response()->json([
                'success' => true,
                'likes_count' => $count,
            ]);
        }

        return response()->json(['success' => false], 401);
    }

    public function destroy(Request $request, $item_id){
        if (auth()->check()) {
            Like::where('user_id', auth()->id())
                ->where('product_id', $item_id)
                ->delete();

            $count = Like::where('product_id', $item_id)->count();
            return response()->json([
                'success' => true,
                'likes_count' => $count,
            ]);
        }

        return response()->json(['success' => false], 401);
    }

}
