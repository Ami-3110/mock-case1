<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;

/*Route::get('/', function () {
    return view('welcome');*/

//認証不要
    //商品一覧表示（トップページ）
    Route::get('/', [ItemController::class, 'index'])->name('items.index');
    //商品詳細表示
    Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');
    //商品検索
    Route::get('/search', [ItemController::class, 'search'])->name('items.search');

    //いいね（付）
    Route::post('/like/{product}', [LikeController::class, 'store'])->name('like.store');
    //いいね（外）
    Route::delete('/like/{product}', [LikeController::class, 'destroy'])->name('like.destroy');

//認証
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/login', [AuthenticatedSessionController::class, 'showLoginForm']);
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');

 
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');    

    //認証要
    Route::middleware(['auth'])->group(function () {
        // 出品画面
        Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
        // 出品処理
        Route::post('/sell', [ItemController::class, 'store'])->name('items.store');
        
        // マイページ画面
        Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');

        // プロフィール編集画面
        Route::get('/mypage/edit', [MypageController::class, 'edit'])->name('mypage.edit');
        // プロフィール更新
        Route::post('/mypage/profile', [MypageController::class, 'updateProfile'])->name('mypage.updateProfile');

        // 購入フォーム表示（商品詳細→購入確認）
        Route::get('/purchase/{item_id}', [PurchaseController::class, 'showForm'])->name('purchase.showForm');
        // 購入処理
        Route::post('/purchase/{item_id}', [PurchaseController::class, 'purchase'])->name('purchase');
       
        // 配送先変更画面（任意で使う場合）
        Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddressForm'])->name('purchase.editAddressForm');
        // 配送先変更処理
        Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.updateAddress');

        // コメント
        Route::post('/comment/{item}', [CommentController::class, 'store'])->name('comments.store');

    });

 // 購入御礼ページ
 Route::get('purchase/thanks', [PurchaseController::class, 'thanks'])->name('purchase.thanks');

 
   // 1. メール認証リンクの処理（ユーザーIDとハッシュが付いた署名付きURLを検証）
//  Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
//   $request->fulfill();  // 認証状態に更新
//   return redirect('/mypage/edit');  // 認証後に行かせたいページへリダイレクト
//    })->middleware(['auth', 'signed'])->name('verification.verify');

    // 2. 認証メールの再送信
//    Route::post('/email/verification-notification', function (Request $request) {
//    $request->user()->sendEmailVerificationNotification();
//    return back()->with('message', '認証メールを再送信しました。');
//    })->middleware(['auth'])->name('verification.send');
    
    // 3. 認証メール送信確認ページ
//    Route::get('/email/verify', function () {
//        return view('auth.verify-email');
//    })->middleware(['auth'])->name('verification.notice');   
