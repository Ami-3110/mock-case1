<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ExhibitionRequest;


class ItemController extends Controller
{
    // 商品一覧
    public function index(Request $request){
        $tab = $request->input('tab', 'recommend');
        $userId = auth()->id();
        $keyword = $request->input('keyword');
    
        if ($tab === 'recommend') {
            $products = Product::where('user_id', '!=', $userId)->get();
    
        } elseif ($tab === 'mylist') {
            if (!auth()->check()) {
                $products = collect();
            } else {
                $likes = auth()->user()
                    ->likes()
                    ->with('product')
                    ->get();
    
                $products = $likes->filter(function ($like) use ($userId, $keyword) {
                    $product = $like->product;
        
                    return $product &&
                        $product->user_id !== $userId &&
                        (!$keyword || str_contains($product->product_name, $keyword));
                });
            }
        }
        
        return view('items.index', [
            'products' => $products,
            'activeTab' => $tab,
            'keyword' => $keyword,
        ]);
    }
    

    // 商品検索
    public function search(Request $request){
        $keyword = $request->input('keyword');
        $products = Product::where('product_name', 'like', '%' . $keyword . '%');
        if (auth()->check()) {
            $products = $products->where('user_id', '!=', auth()->id());
        }
        $products = $products->get();
    
        return view('items.index', [
            'products' => $products,
            'keyword' => $keyword,
            'activeTab' => 'search',
        ]);
    }
    
    

    // 商品詳細
    public function show($item_id){
        $item = Product::with(['user', 'likes', 'comments.user', 'categories'])
            ->withCount(['likes', 'comments'])
            ->findOrFail($item_id);
    
        $user = auth()->user();
        $isLiked = $user ? $user->likes()->where('product_id', $item->id)->exists() : false;
    
        return view('items.show', compact('item', 'isLiked'));
    }
    

    // 出品フォーム表示
    public function create(){
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    // 出品処理
    public function store(ExhibitionRequest $request){
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

        $product->categories()->sync($request->category);

        return redirect('/mypage?tab=sell');
    }
}
