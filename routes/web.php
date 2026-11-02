<?php


use Illuminate\Support\Facades\Route;



// breezeの認証機能
// use App\Http\Controllers\Admin\LoginController;
// use App\Http\Controllers\Admin\DashboardController;
// use App\Http\Controllers\Admin\Auth\RegisterController;
// admin.phpはbootstrap/app.phpで読み込まれるため、ここでは読み込まない
// require __DIR__.'/admin.php';
require __DIR__.'/item.php';
require __DIR__.'/member.php';
require __DIR__.'/api.php';  // APIルートの読み込みを追加


// use App\Http\Controllers\Admin\AdminDashboardController;



/** フロント用ルート設定
 *  @view
 *  return void
 */

use App\Http\Controllers\SitemapController;
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

use App\Http\Controllers\AgeVerificationController;
Route::get('/', [AgeVerificationController::class, 'index'])->name('age_verification');
Route::post('/age-verify', [AgeVerificationController::class, 'verify'])->name('age.verify');

use App\Http\Controllers\IndexController;
Route::get('/top', [IndexController::class, 'index'])->name('top');

use App\Http\Controllers\ItemController;
Route::get('/items', [ItemController::class,'index']) ->name('item.index');
Route::get('/items/{id?}', [ItemController::class, 'show'])->name('items.show');
Route::get('/x', [ItemController::class, 'twitter'])->name('x.index');
Route::get('/movie', [ItemController::class, 'movie'])->name('movie.index');
Route::get('/newface', [ItemController::class, 'newface'])->name('newface.index');

use App\Http\Controllers\ScheduleController;
Route::get('/schedule/{date?}', [ScheduleController::class, 'index'])->name('schedule');
// Route::get('/schedule/{date}', [ScheduleController::class, 'dayList'])->name('schedule.day');
// 新規追加：日付を選んだときだけ出勤リスト返す用
// Route::get('/schedule-data/{date}', [ScheduleController::class, 'dayList'])->name('schedule.dayList');

use App\Http\Controllers\RankController;
Route::get('/rank/{cate_id?}', [RankController::class, 'index'])->name('rank.index');


use App\Http\Controllers\CostumeController;
Route::get('/costume', [CostumeController::class, 'index'])->name('costumes.index');

use App\Http\Controllers\SystemController;
Route::get('/system', [SystemController::class, 'index'])->name('systems');
use App\Http\Controllers\HotelController;
Route::get('/hotel', [HotelController::class, 'index'])->name('hotels');
Route::get('/access', function () {
    return view('access');
})->name('access');

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

use App\Http\Controllers\ContactController;
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact/confirm', [ContactController::class, 'confirm'])->name('contact.confirm');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
Route::get('/contact/complete', [ContactController::class, 'complete'])->name('contact.complete');

use App\Http\Controllers\EventController;
Route::get('/event', [EventController::class, 'index'])->name('events.index');
use App\Http\Controllers\RecruitController;
Route::get('/recruit', [RecruitController::class, 'index'])->name('recruits.index');
Route::post('/recruit/submit', [RecruitController::class, 'submit'])->name('recruit.submit');

use App\Http\Controllers\ReservationController;
// Route::get('/reservation', [ReservationController::class, 'index'])->name('reservation.index');
Route::get('/get-course-fees/{course}', [ReservationController::class, 'getCourseFees']);
Route::get('/get-course-fees/{courseId}', [ReservationController::class, 'getCourseFees'])->name('get-course-fees');
Route::get('/get-cast-items/{itemId}', [ReservationController::class, 'getCastItems'])->name('get-cast-items');

Route::get('/reservation', [ReservationController::class, 'index'])->name('reservation.index');
Route::match(['get', 'post'], '/reservation/confirm', [ReservationController::class, 'confirm'])->name('reservation.confirm');
Route::post('/reservation', [ReservationController::class, 'store'])->name('reservation.store');
Route::get('/reservation/thanks', [ReservationController::class, 'thanks'])->name('reservation.thanks');

// 予約関連のルート
Route::get('/reservation/select', [ReservationController::class, 'select'])->name('reservation.select');
// Route::get('/reservation/form', [ReservationController::class, 'form'])->name('reservation.form');
// Route::post('/reservation', [ReservationController::class, 'store'])->name('reservation.store');
Route::get('/reservation/complete', [ReservationController::class, 'complete'])->name('reservation.complete');

use App\Http\Controllers\FanclubController;
Route::get('/fanclub', [FanclubController::class, 'index'])->name('fanclub');
Route::get('/fanclub/{fanclub}', [FanclubController::class, 'show'])->name('fanclub.show');
Route::get('/legal/specified-commercial-transactions', [FanclubController::class, 'commerceLaw'])->name('legal.sct');

use App\Http\Controllers\GalleryController;
Route::get('/galleries', [GalleryController::class, 'index'])->name('gallery.index') ;
Route::get('/galleries/{gallery}', [GalleryController::class, 'show'])->name('gallery.show');

use App\Http\Controllers\NewsController;
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{id}', [NewsController::class, 'show'])->name('news.show');

// 特設イベント
use App\Http\Controllers\SpecialEventController;
use App\Http\Controllers\CouponController;
Route::get('/special-event', [SpecialEventController::class, 'index'])->name('specialEvents.index');
Route::get('/special-event/{id}', [SpecialEventController::class, 'show'])->name('specialEvents.show');

// クーポン
Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.index');
Route::get('/coupons/{id}', [CouponController::class, 'show'])->name('coupons.show');
// ジャーナル
use App\Http\Controllers\BlogController;
Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
Route::get('/blogs/{id}', [BlogController::class, 'show'])->name('blogs.show');

// 使い方ガイド
use App\Http\Controllers\GuidanceController;
Route::get('/guidance', [GuidanceController::class, 'index'])->name('guidance.index');

// オプション一覧
use App\Http\Controllers\OptionsController;
Route::get('/options', [OptionsController::class, 'index'])->name('options');


require __DIR__.'/auth.php';

// Mailgun Webhook（認証不要）
use App\Http\Controllers\MailgunWebhookController;
Route::post('/webhooks/mailgun', [MailgunWebhookController::class, 'handleIncoming'])->name('webhooks.mailgun');

// Square Webhook（認証不要・CSRF除外は VerifyCsrfToken::$except で設定済み）
use App\Http\Controllers\Api\SquareWebhookController;
Route::post('/payment/webhook/square', [SquareWebhookController::class, 'handle'])->name('payment.webhook.square');

// 会員ダッシュボードへのエイリアス（RouteServiceProvider::HOME や旧リンク用）
Route::get('/dashboard', function () {
    return redirect()->route('member.dashboard');
})->middleware(['auth:member'])->name('dashboard');

// メールマガジン登録
use App\Http\Controllers\NewsletterController;
Route::get('/newsletter/register', [NewsletterController::class, 'showRegisterForm'])->name('newsletter.register');
Route::post('/newsletter/register', [NewsletterController::class, 'register'])
    ->middleware(['throttle:5,60'])->name('newsletter.register.post');
Route::get('/newsletter/confirm/{token}', [NewsletterController::class, 'confirm'])->name('newsletter.confirm');
Route::get('/newsletter/unsubscribe/{token}', [NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');


