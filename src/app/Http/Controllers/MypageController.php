<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Category;
use App\Models\Purchase;
use App\Models\UserProfile;
use App\Models\Comment;
use App\Models\Like;
use App\Models\User;
use App\Http\Requests\AddressRequest;
use App\Http\Requests\ProfileRequest;

class MypageController extends Controller
{
    // プロフィール画面
    public function index(Request $request){
        $user = auth()->user();
        $tab = $request->query('tab', 'sell'); // デフォルトは出品

        $sellItems = Product::where('user_id', $user->id)->get();
        $buyItems = Purchase::where('user_id', $user->id)->with('product')->get();

        return view('mypage.index', compact('user', 'sellItems', 'buyItems', 'tab'));
    }

    // プロフィール編集画面
    public function edit(){
        $user = auth()->user()->load('userProfile');
        return view('mypage.edit', compact('user'));
    }

    // プロフィール更新処理
    public function updateProfile(AddressRequest $request){
        $user = auth()->user();

        $profileRequest = new ProfileRequest();
        $validator = Validator::make($request->all(), $profileRequest->rules(), $profileRequest->messages());

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // プRpフィール画像がなければ新規作成
        $profile = $user->userProfile ?? new \App\Models\UserProfile(['user_id' => $user->id]);
        if ($request->hasFile('user_image')) {
            $path = $request->file('user_image')->store('profile_images', 'public');
            $profile->profile_image = $path;
            $user->userProfile->save();
        }
    
        $profile->postal_code = $request->postal_code;
        $profile->address = $request->address;
        $profile->building = $request->building;
        $profile->save();
    
        // 必要に応じてユーザー名も更新
        if ($request->filled('user_name')) {
            $user->user_name = $request->user_name;
            $user->save();
        }
        return redirect('/mypage')->with('success', 'プロフィールを更新しました。');
    }
    
}
