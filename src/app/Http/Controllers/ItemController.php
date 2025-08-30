<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Str;


class ItemController extends Controller
{
    // 共通：キーワードに大小無視で部分一致
    private function matchesKeyword(?string $text, ?string $keyword): bool
    {
        if (!$keyword) return true;
        if ($text === null) return false;

        return Str::of($text)->lower()->contains(Str::lower($keyword));
    }

    // 商品一覧
    public function index(Request $request)
    {
        $tab     = $request->input('tab', 'recommend');
        $userId  = auth()->id();
        $keyword = $request->input('keyword');

        if ($tab === 'recommend') {
            $products = \App\Models\Product::query()
                ->when($userId, fn($q) => $q->where('user_id', '!=', $userId))
                ->withExists(['trades as has_active_trade' => function ($q) {
                    $q->whereIn('status', ['trading', 'buyer_completed', 'completed']);
                }])
                ->latest()
                ->get();

        } elseif ($tab === 'mylist') {
            if (!auth()->check()) {
                $products = collect();
            } else {
                $productIds = auth()->user()->likes()->pluck('product_id');

                $products = \App\Models\Product::query()
                    ->whereIn('id', $productIds)
                    ->where('user_id', '!=', $userId)
                    ->when($keyword, fn($q) => $q->where('product_name', 'like', "%{$keyword}%"))
                    ->withExists(['trades as has_active_trade' => function ($q) {
                        $q->whereIn('status', ['trading', 'buyer_completed', 'completed']);
                    }])
                    ->latest()
                    ->get();
            }
        } else {
            $products = collect();
        }

        return view('items.index', [
            'products'  => $products,
            'activeTab' => $tab,
            'keyword'   => $keyword,
        ]);
}


    // 商品検索
    public function search(Request $request)
    {
        $keyword = $request->input('keyword');

        $kw = $keyword ? mb_strtolower($keyword, 'UTF-8') : null;

        $products = Product::query()
            ->when($kw, function ($q) use ($kw) {
                $q->whereRaw('LOWER(product_name) LIKE ?', ['%'.$kw.'%']);
            })
            ->when(auth()->check(), function ($q) {
                $q->where('user_id', '!=', auth()->id());
            })
            ->get();

        return view('items.index', [
            'products'  => $products,
            'keyword'   => $keyword,
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
