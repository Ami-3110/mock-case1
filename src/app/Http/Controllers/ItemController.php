<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\UserProfile;
use App\Models\Comment;
use App\Models\Like;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class ItemController extends Controller
{
    // 商品一覧（全体）
    public function index(Request $request){
        $tab = $request->input('tab', 'recommend');
        $userId = auth()->id();
    
        if ($tab === 'recommend') {
            // 自分以外の商品すべて
            $products = Product::where('user_id', '!=', $userId)->get();
    
        } elseif ($tab === 'mylist') {
            // Likeを先に取得し、そこからproductにアクセス
            $likes = auth()->user()
                ->likes()
                ->with('product')
                ->get();
    
            // Productが存在していて、かつ出品者が自分じゃないものだけ取り出す
            $products = $likes->filter(function ($like) use ($userId) {
                return $like->product && $like->product->user_id !== $userId;
            });
        }
    
        return view('items.index', [
            'products' => $products,
            'activeTab' => $tab,
        ]);
    }
    


    // 商品検索
    public function search(Request $request){
        $keyword = $request->input('keyword');
        $items = Product::where('name', 'like', '%' . $keyword . '%')->get();
        return view('items.index', compact('items', 'keyword'));
    }

    // 商品詳細
    public function show($item_id){
        $item = Product::with(['user', 'likes', 'comments.user', 'categories'])
            ->withCount(['likes', 'comments'])
            ->findOrFail($item_id);
    
        $user = auth()->user();
        $isLiked = $user ? $user->likes()->where('product_id', $item->id)->exists() : false;
        $likeSymbol = $isLiked ? '★' : '☆';
    
        return view('items.show', compact('item', 'isLiked', 'likeSymbol'));
    }
    

    // 出品フォーム表示
    public function create(){
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    // 出品処理
    public function store(Request $request){
        $path = $request->file('product_image')->store('products', 'public');
        $product = Product::create([
            'user_id' => Auth::id(),
            'product_name' => $request->product_name,
            'product_image' => $path,
            'condition' => $request->condition,
            'brand' => $request->brand,
            'description' => $request->description,
            'price' => $request->price,
            'is_sold' => false,
        ]);

        $product->categories()->sync($request->category_ids);

        return redirect('/mypage?tab=sell')->with('success', '商品を出品しました！');
    }


}
