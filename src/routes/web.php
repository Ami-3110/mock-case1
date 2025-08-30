<?php
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ItemController, MypageController, PurchaseController,
    Auth\RegisteredUserController, Auth\AuthenticatedSessionController,
    LikeController, CommentController, TradeController,
    TradeMessageController, TradeCompleteController, TradeRatingController
};

// 認証不要
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');
Route::get('/search', [ItemController::class, 'search'])->name('items.search');
Route::get('/thanks', [PurchaseController::class, 'thanks'])->name('purchase.thanks');

// 認証関連
Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::get('/login',  [AuthenticatedSessionController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.post');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// メール認証コールバック
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('mypage.edit');
})->middleware(['signed','throttle:6,1'])->name('verification.verify');

// 認証必須
Route::middleware(['auth','verified'])->group(function () {

    // マイページ
    Route::get('/mypage', [MypageController::class, 'index'])->name('mypage.index');
    Route::get('/mypage/edit', [MypageController::class, 'edit'])->name('mypage.edit');
    Route::post('/mypage/profile', [MypageController::class, 'updateProfile'])->name('mypage.updateProfile');

    // コメント
    Route::post('/comment/{item}', [CommentController::class, 'store'])->name('comments.store');

    // 出品
    Route::get('/sell', [ItemController::class, 'create'])->name('items.create');
    Route::post('/sell', [ItemController::class, 'store'])->name('items.store');

    // 購入フロー（Stripe）
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'showForm'])->name('purchase.showForm');
    Route::post('/purchase/confirm/{item_id}', [PurchaseController::class, 'confirm'])->name('purchase.confirm');
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddressForm'])->name('purchase.editAddressForm');
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.updateAddress');

    // いいね（認証者のみ操作可にする）
    Route::post('/like/{item_id}',   [LikeController::class, 'store'])->name('like.store');
    Route::delete('/like/{item_id}', [LikeController::class, 'destroy'])->name('like.destroy');

    // 取引/チャット
    Route::get('/trades/{trade}', [TradeController::class, 'show'])->name('trades.show');

    Route::post('/trades/{trade}/messages', [TradeMessageController::class, 'store'])->name('chat.store');
    Route::patch('/messages/{message}',     [TradeMessageController::class, 'update'])->name('chat.update');
    Route::delete('/messages/{message}',    [TradeMessageController::class, 'destroy'])->name('chat.destroy');

    // 完了/評価
    Route::post('/trades/{trade}/complete', [TradeCompleteController::class, 'store'])->name('trade.complete');
    Route::post('/trades/{trade}/ratings',  [TradeRatingController::class, 'store'])->name('trade.ratings.store');
});

