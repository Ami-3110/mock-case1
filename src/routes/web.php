<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MypageController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TradeController;

/*Route::get('/', function () {
    return view('welcome');*/

//認証不要
    //商品一覧表示（トップページ）
    Route::get('/', [ItemController::class, 'index'])->name('items.index');
    //商品詳細表示
    Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');
    //商品検索
    Route::get('/search', [ItemController::class, 'search'])->name('items.search');
    // いいね追加
    Route::post('/like/{item_id}', [LikeController::class, 'store'])->name('like.store');
    // いいね解除
    Route::delete('/like/{item_id}', [LikeController::class, 'destroy'])->name('like.destroy');
    // 購入御礼ページ
    Route::get('/thanks', [PurchaseController::class, 'thanks'])->name('purchase.thanks');

//認証関連
    // 会員登録画面
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    // 会員登録処理
    Route::post('/register', [RegisteredUserController::class, 'store']);    
    // ログイン画面
    Route::get('/login', [AuthenticatedSessionController::class, 'showLoginForm']);
    // ログイン処理
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');
    // ログアウト処理
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    // メール認証を踏んだらログイン状態、かつプロフィール登録画面にかっ飛ばす
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('mypage.edit');
    })->middleware(['signed'])->name('verification.verify');

//認証要
    Route::middleware(['auth', 'verified'])->group(function () {
        // マイページ画面
        Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');
        // プロフィール編集画面
        Route::get('/mypage/edit', [MypageController::class, 'edit'])->name('mypage.edit');
        // プロフィール更新処理
        Route::post('/mypage/profile', [MypageController::class, 'updateProfile'])->name('mypage.updateProfile');
        // コメント
        Route::post('/comment/{item}', [CommentController::class, 'store'])->name('comments.store');
        // 出品画面
        Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
        // 出品処理
        Route::post('/sell', [ItemController::class, 'store'])->name('items.store');
        // 購入フォーム表示（商品詳細→購入確認）
        Route::get('/purchase/{item_id}', [PurchaseController::class, 'showForm'])->name('purchase.showForm');
        // 購入処理
        // Route::post('/purchase/{item_id}', [PurchaseController::class, 'purchase'])->name('purchase.purchase');
        // stripe
        Route::post('/purchase/confirm/{item_id}', [PurchaseController::class, 'confirm'])->name('purchase.confirm');
        // 配送先変更画面
        Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddressForm'])->name('purchase.editAddressForm');
        // 配送先変更処理
        Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.updateAddress');

        // 個別チャット表示
        Route::get('/trades/{trade}', [TradeController::class, 'show'])->name('trades.show');

        Route::middleware('auth')->group(function () {
            Route::get('/trades/{trade}', [\App\Http\Controllers\TradeController::class, 'show'])->name('trades.show');

            // チャット投稿・編集・削除
            Route::post('/trades/{trade}/messages', [\App\Http\Controllers\TradeMessageController::class, 'store'])->name('chat.store');
            Route::patch('/messages/{message}', [\App\Http\Controllers\TradeMessageController::class, 'update'])->name('chat.update');
            Route::delete('/messages/{message}', [\App\Http\Controllers\TradeMessageController::class, 'destroy'])->name('chat.destroy');

            // 取引完了（ダミーでもOK・既存あれば不要）
            Route::post('/trades/{trade}/complete', [\App\Http\Controllers\TradeCompleteController::class, 'store'])->name('trade.complete');
        });

    });
