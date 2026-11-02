<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Member\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Member\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Member\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Member\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Member\Auth\NewPasswordController;
use App\Http\Controllers\Member\Auth\PasswordController;
use App\Http\Controllers\Member\Auth\PasswordResetLinkController;
use App\Http\Controllers\Member\Auth\RegisteredUserController;
// use App\Http\Controllers\Member\Auth\VerifyEmailController;
use App\Http\Controllers\Member\MemberController;
use App\Http\Controllers\Member\DashboardController;
use App\Http\Controllers\Member\HistoryController;
use App\Http\Controllers\Member\AccountController;
use App\Http\Controllers\Member\GalleryController;
// use App\Http\Controllers\Member\MailController;
// use App\Http\Controllers\Member\TelController;
// use App\Http\Controllers\Member\OthersController;
use App\Http\Controllers\Member\RegisterController;
use App\Http\Controllers\Member\VerifyEmailController;
use App\Http\Controllers\Member\ResendVerificationController;
use App\Http\Controllers\Member\MessageController;
use App\Http\Controllers\Member\PointHistoryController;
use App\Http\Controllers\Member\FanclubMembershipController;
use App\Http\Controllers\Member\FanclubContentController;
use App\Http\Controllers\Member\FanclubMessageController;
use App\Http\Controllers\Member\PointPurchaseController;


// 未ログインユーザー用のルート
Route::middleware('guest:member')->group(function () {
    // /member へのアクセスをログインページにリダイレクト
    Route::get('member', function () {
        return redirect('member/login');
    });

    Route::prefix('member')->name('member.')->group(function () {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])
            ->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store']);

        Route::get('register', [RegisterController::class, 'showForm'])
            ->name('register');
        Route::post('register', [RegisterController::class, 'register'])
            ->name('register.submit')
            ->middleware(['registration.rate.limit', 'honeypot']);

        Route::get('verify/email/{id}', VerifyEmailController::class)
            ->name('verify.email')
            ->middleware('signed');

        Route::get('email-sent', function () {
            $shops = \App\Models\Shop::first();
            $email = session('email', ''); // セッションからメールアドレスを取得
            return view('member.auth.email-sent', compact('shops', 'email'));
        })->name('email.sent');

        Route::post('resend-verification', ResendVerificationController::class)
            ->middleware('throttle:3,1')
            ->name('resend.verification');

        Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
            ->name('password.request');
        Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
            ->name('password.email');

        Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
            ->name('password.reset');

        Route::post('reset-password', [NewPasswordController::class, 'store'])
            ->name('password.store');
    });
});

// 登録完了ページ（認証状態に関わらずアクセス可能）
Route::get('member/registration-complete', function () {
    $shops = \App\Models\Shop::first();
    return view('member.auth.registration-complete', compact('shops'));
})->name('member.registration.complete');

// テスト用ルート
Route::get('member/test-verify/{id}', function ($id) {
    \Log::info('Test verify route accessed', ['id' => $id]);

    return response('
        <!DOCTYPE html>
        <html>
        <head>
            <title>テスト認証</title>
            <meta charset="utf-8">
        </head>
        <body>
            <h1>テスト認証ページ</h1>
            <p>ID: ' . $id . '</p>
            <p>このページが表示されれば、ルートは正常に動作しています。</p>
        </body>
        </html>
    ');
})->name('member.test.verify');

// ログイン済みユーザー用のルート
Route::middleware('auth:member')->group(function () {
    Route::prefix('member')->name('member.')->group(function () {
        Route::get('/', function () {
            return redirect()->route('member.dashboard');
        });

        Route::get('dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        // ファンクラブ
        Route::get('fanclubs', [FanclubMembershipController::class, 'index'])->name('fanclubs.index');
        Route::get('fanclubs/joined', [FanclubMembershipController::class, 'joined'])->name('fanclubs.joined');
        Route::get('fanclubs/contents', [FanclubMembershipController::class, 'contents'])->name('fanclubs.contents');
        Route::get('fanclubs/{fanclub}/subscribe/confirm', [FanclubMembershipController::class, 'confirmSubscribe'])->name('fanclubs.subscribe.confirm');
        Route::post('fanclubs/{fanclub}/subscribe', [FanclubMembershipController::class, 'subscribe'])->name('fanclubs.subscribe');
        Route::get('fanclubs/{fanclub}/subscribe/complete', [FanclubMembershipController::class, 'subscribeComplete'])->name('fanclubs.subscribe.complete');
        Route::get('fanclubs/{fanclub}/withdraw/confirm', [FanclubMembershipController::class, 'confirmWithdraw'])->name('fanclubs.withdraw.confirm');
        Route::patch('fanclubs/{fanclub}/membership', [FanclubMembershipController::class, 'updateStatus'])->name('fanclubs.membership.update');
        Route::get('fanclubs/{fanclub}', [FanclubContentController::class, 'show'])->name('fanclubs.show');
        Route::get('fanclubs/{fanclub}/messages', [FanclubMessageController::class, 'index'])->name('fanclubs.messages.index');
        Route::post('fanclubs/{fanclub}/messages', [FanclubMessageController::class, 'store'])->name('fanclubs.messages.store');
        Route::delete('fanclubs/{fanclub}/messages/{fanclubMessage}', [FanclubMessageController::class, 'destroy'])->name('fanclubs.messages.destroy');

        Route::get('verify-email', EmailVerificationPromptController::class)
            ->name('verification.notice');

        Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
            ->middleware(['signed', 'throttle:6,1'])
            ->name('verification.verify');

        Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
            ->middleware('throttle:6,1')
            ->name('verification.send');

        Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
            ->name('password.confirm');

        Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

        Route::put('password', [PasswordController::class, 'update'])
            ->name('password.update');

        Route::get('logout', [AuthenticatedSessionController::class, 'confirmLogout'])
            ->name('logout.confirm');
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');

        ////////////////////////////////////////////////////
        // 表示のみのルート

        // 履歴管理
        Route::get('history', [HistoryController::class, 'index'])
            ->name('history');

        // ポイント履歴
        Route::get('point-history', [PointHistoryController::class, 'index'])
            ->name('point-history');

        // ポイント購入
        Route::get('points/purchase', [PointPurchaseController::class, 'index'])->name('points.purchase');
        Route::get('points/purchase/confirm', [PointPurchaseController::class, 'confirm'])->name('points.purchase.confirm');
        Route::post('points/purchase', [PointPurchaseController::class, 'purchase'])->name('points.purchase.post');
        Route::get('points/purchase/complete', [PointPurchaseController::class, 'complete'])->name('points.purchase.complete');

        ////////////////////////////////////////////////////
        // 情報編集のルート
        // Route::resource('members', EditItemController::class);
        Route::get('account/password', [AccountController::class, 'password'])
            ->name('account.password');
        Route::patch('account/password/{id}', [AccountController::class, 'passwordUpdate'])
            ->name('account.passwordUpdate');
        Route::get('/account', [AccountController::class, 'account'])->middleware('auth:member')->name('account');
        Route::patch('/account/{id}', [AccountController::class, 'accountUpdate'])->middleware('auth:member')->name('accountUpdate');

        ////////////////////////////////////////////////////
        // ギャラリー
        Route::resource('gallery', GalleryController::class);
        Route::post('/gallery/sort', [GalleryController::class, 'sort'])->name('gallery.sort');
        Route::post('/gallery/{gallery}/images', [GalleryController::class, 'uploadImages'])->name('gallery.image.upload');
        Route::delete('/gallery/{gallery}/images/{image}', [GalleryController::class, 'destroyImage'])->name('gallery.image.destroy');
        Route::post('gallery/{gallery}/images/sort', [GalleryController::class, 'sortImages'])->name('gallery.image.sort');

        ////////////////////////////////////////////////////
        // メッセージ
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [MessageController::class, 'index'])->name('index');
            Route::post('/', [MessageController::class, 'store'])->name('store');
            Route::post('/{message}/read', [MessageController::class, 'markAsRead'])->name('read');
            Route::get('/unread-count', [MessageController::class, 'getUnreadCount'])->name('unread-count');
        });

        // メッセージ関連のルート
        Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
        Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
        Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
        Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');

    });
});
