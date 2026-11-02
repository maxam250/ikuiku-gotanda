<?php

use App\Http\Controllers\Item\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Item\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Item\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Item\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Item\Auth\NewPasswordController;
use App\Http\Controllers\Item\Auth\PasswordController;
use App\Http\Controllers\Item\Auth\PasswordResetLinkController;
use App\Http\Controllers\Item\Auth\RegisteredUserController;
use App\Http\Controllers\Item\Auth\VerifyEmailController;
use App\Http\Controllers\Item\ItemController;
use App\Http\Controllers\Item\DashboardController;
use Illuminate\Support\Facades\Route;


//未ログイン

Route::middleware('guest:item')->group(function () {


    Route::get('/', function () {
        return redirect()->route('item.login');
    });


    Route::prefix('item')->name('item.')->group(function () {


        // Route::get('register', [RegisteredUserController::class, 'create'])
        //     ->name('register');

        // Route::post('register', [RegisteredUserController::class, 'store']);

        Route::get('login', [AuthenticatedSessionController::class, 'create'])
            ->name('login');

        Route::post('login', [AuthenticatedSessionController::class, 'store']);

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


// use App\Http\Controllers\ImageController;
use App\Http\Controllers\Item\ItemImageController;
use App\Http\Controllers\Item\ScheduleController;
use App\Http\Controllers\Item\ScheduleMonthController;
use App\Http\Controllers\Item\EventController;
use App\Http\Controllers\Item\NewsController;
use App\Http\Controllers\Item\OptionController;
use App\Http\Controllers\Item\OptionPayController;
use App\Http\Controllers\Item\OptionCateController;
use App\Http\Controllers\Item\ItemOptionController;
use App\Http\Controllers\Item\ItemOptionPayController;
use App\Http\Controllers\Item\ProfileController;
// use App\Http\Controllers\Item\IconItemController;
// use App\Http\Controllers\Item\ItemIconItemController;
use App\Http\Controllers\Item\ItemProfileController;
use App\Http\Controllers\Item\MycostumeController;
use App\Http\Controllers\Item\MycostumeCateController;
use App\Http\Controllers\Item\MyoptionController;
use App\Http\Controllers\Item\MyoptionCateController;

// use App\Http\Controllers\Item\ShopController;
// use App\Http\Controllers\Item\CourseController;
// use App\Http\Controllers\Item\CourseFeeController;

use App\Http\Controllers\Item\ReservationController;
use App\Http\Controllers\Item\HistoryController_old;
use App\Http\Controllers\Item\GalleryController;
use App\Http\Controllers\Item\MessageController;
use App\Http\Controllers\Item\HistoryController;

Route::middleware('auth:item')->group(function () {

    Route::get('/', function () {
        return redirect('/item/dashboard');
    });


    Route::prefix('item')->name('item.')->group(function () {

        Route::get('/item', function () {
            return redirect('/item/dashboard');
        })->middleware('item');


        Route::get('dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');

        Route::patch('/dashboard/{id}/realtimeCommentUpdate', [DashboardController::class, 'updateRealtimeComment'])->name('updateRealtimeComment');

        Route::get('reservation-history', [HistoryController::class, 'index'])
            ->name('reservation-history');

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

        Route::put('password', [PasswordController::class, 'update'])->name('password.update');

        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
            ->name('logout');




        //キャスト画像アップロード用
        Route::post('/items/upload-image', [ItemImageController::class, 'uploadImage'])->name('items.upload-image');

        //画像の更新
        Route::post('/items/images/{imageId}/replace', [ItemImageController::class, 'replaceImage']);

        //画像の削除
        Route::delete('/items/images/{imageId}', [ItemImageController::class, 'deleteImage']);

        // 画像のソート
        Route::post('/items/sort-image', [ItemImageController::class, 'sortImage'])->name('items.sort-image');

        // サムネイルの設定
        Route::post('/items/set-thumbnail', [ItemImageController::class, 'setThumbnail']);


        ////////////////////////////////////////////////////
        // キャスト管理
        // Route::resource('items', ItemController::class);
        Route::get('/items/edit', [ItemController::class, 'edit'])->middleware('auth:item')->name('items.edit');
        Route::get('/items/coupons', [ItemController::class, 'coupons'])->middleware('auth:item')->name('items.coupons');
        Route::get('/items/account', [ItemController::class, 'account'])->middleware('auth:item')->name('items.account');
        Route::patch('/items/{id}', [ItemController::class, 'update'])->middleware('auth:item')->name('items.update');
        Route::patch('/items/account/{id}', [ItemController::class, 'accountUpdate'])->middleware('auth:item')->name('items.account.accountUpdate');


        //キャスト並び替え
        Route::post('/items/sort', [ItemController::class, 'sort'])->name('items.sort');
        //キャストパスワード
        // Route::post('/items/password', [ItemController::class, 'password'])->name('items.password');

        //プロフィール項目
        Route::post('/items/profile', [ItemController::class, 'profile'])->name('items.profile');
        Route::patch('/items/{id}/update-profile', [ItemController::class, 'updateProfile'])->name('items.updateProfile');


        // ProfileをAjaxで更新するルート
        // Route::post('/item/items/{id}/update-profile', [ItemController::class, 'updateProfile'])->name('items.updateProfile');

        // OptionをAjaxで更新するルート
        Route::post('/item/items/{id}/update-option', [ItemController::class, 'updateOption'])->name('items.updateOption');

        // OptionPayをAjaxで更新するルート
        Route::post('/item/items/{id}/update-option-pay', [ItemController::class, 'updateOptionPay'])->name('items.updateOptionPay');

        // IconItemをAjaxで更新するルート
        Route::post('/item/items/{id}/update-icon-item', [ItemController::class, 'updateIconItem'])->name('items.updateIconItem');

        //Q&A項目
        Route::patch('/items/{id}/update-interview', [ItemController::class, 'updateInterview'])->name('items.updateInterview');

        // コース設定をAjaxで更新するルート
        Route::post('/item/items/{id}/update-course', [ItemController::class, 'updateCourse'])->name('items.updateCourse');

        // 料金設定をAjaxで更新するルート
        Route::post('/item/items/{id}/update-course-fee', [ItemController::class, 'updateCourseFee'])->name('items.updateCourseFee');

        // クーポン設定をAjaxで更新するルート
        Route::post('/items/update-coupons', [ItemController::class, 'updateCoupons'])->name('items.updateCoupons');



        ////////////////////////////////////////////////////
        // スケジュール管理
        // Route::get('/schedules/{date?}', [ScheduleController::class, 'index'])->name('schedules.index');
        // Route::put('/schedules/{id}/status', [ScheduleController::class, 'updateStatus'])->name('schedules.updateStatus');
        // Route::put('/schedules/{id}/update-time', [ScheduleController::class, 'updateTime'])->name('schedules.updateTime');
        // Route::post('/schedules/bulk-update/{date?}', [ScheduleController::class, 'bulkUpdate'])->name('schedules.bulkUpdate');
        // Route::post('/schedules/sort', [scheduleController::class, 'sort'])->name('schedules.sort');
        // Route::delete('/schedules/{id}', [scheduleController::class, 'destroy'])->name('schedules.destroy');

        ////////////////////////////////////////////////////
        // 月間スケジュール管理
        Route::get('/schedules_month/{year?}/{month?}', [ScheduleMonthController::class, 'index'])->name('schedules_month.index');
        // Route::get('/schedules_month/{itemId}', [ScheduleMonthController::class, 'index'])->name('schedules.index');
        Route::post('/schedules_month/toggle', [ScheduleMonthController::class, 'toggleSchedule'])->name('schedules_month.toggle');
        Route::post('/schedules_month/update', [ScheduleMonthController::class, 'updateSchedule'])->name('schedules_month.update');
        Route::delete('/schedules_month/{id}', [ScheduleMonthController::class, 'destroy'])->name('schedules_month.destroy');
        Route::post('/schedules_month/cancel-request', [ScheduleMonthController::class, 'cancelRequest'])->name('schedules_month.cancel_request');
        Route::post('/schedules_month/update-default-times', [ScheduleMonthController::class, 'updateDefaultTimes'])->name('schedules_month.update-default-times');

        ////////////////////////////////////////////////////
        // ピックアップ
        // Route::get('/pickups', [PickupController::class, 'index'])->name('pickups.index');
        // Route::post('/pickups/bulk-update', [PickupController::class, 'bulkUpdate'])->name('pickups.bulkUpdate');
        // Route::post('/pickups/sort', [PickupController::class, 'sort'])->name('pickups.sort');
        // Route::delete('/pickups/{id}', [PickupController::class, 'destroy'])->name('pickups.destroy');


        ////////////////////////////////////////////////////
        // イベント
        // Route::resource('/events', EventController::class);
        // Route::post('/events/sort', [EventController::class, 'sort'])->name('events.sort');


        /*
        GET /item/events（index）: イベントの一覧を表示
        GET /item/events/create（create）: 新しいイベントの作成フォーム
        POST /item/events（store）: 新しいイベントを保存
        GET /item/events/{event}（show）: 特定のイベントを表示
        GET /item/events/{event}/edit（edit）: 特定のイベントを編集するフォーム
        PUT/PATCH /item/events/{event}（update）: 特定のイベントを更新
        DELETE /item/events/{event}（destroy）: 特定のイベントを削除
        */


        ////////////////////////////////////////////////////
        // ニュース/インフォメーション
        // Route::resource('/news', NewsController::class);
        // Route::post('/news/sort', [NewsController::class, 'sort'])->name('news.sort');

        ////////////////////////////////////////////////////
        // リンク
        // Route::resource('/links', LinkController::class);
        // Route::post('/links/sort', [LinkController::class, 'sort'])->name('links.sort');

        ////////////////////////////////////////////////////
        // プロフィール
        // Route::resource('/profiles', ProfileController::class);
        // Route::post('/profiles/sort', [ProfileController::class, 'sort'])->name('profiles.sort');

        ////////////////////////////////////////////////////
        // キャストアイコン
        // Route::resource('/icon_items', IconItemController::class);
        // Route::post('/icon_items/sort', [IconItemController::class, 'sort'])->name('icon_items.sort');

        ////////////////////////////////////////////////////
        // オプション
        // Route::resource('/options', OptionController::class);
        // Route::post('/options/sort', [OptionController::class, 'sort'])->name('options.sort');

        ////////////////////////////////////////////////////
        // オプションPAY
        // Route::resource('/option_pays', OptionPayController::class);
        // Route::post('/option_pays/sort', [OptionPayController::class, 'sort'])->name('option_pays.sort');

        ////////////////////////////////////////////////////
        // オプションカテゴリ
        // Route::resource('/option_cates', OptionCateController::class);
        // Route::post('/option_cates/sort', [OptionCateController::class, 'sort'])->name('option_cates.sort');

        ////////////////////////////////////////////////////
        // キャストに対してオプション等の設定
        // Route::get('items/{item}/options', [ItemOptionController::class, 'edit'])->name('items.options.edit');
        // Route::post('items/{item}/options', [ItemOptionController::class, 'update'])->name('items.options.update');

        // Route::get('items/{item}/option_pays', [ItemOptionPayController::class, 'edit'])->name('items.option_pays.edit');
        // Route::post('items/{item}/option_pays', [ItemOptionPayController::class, 'update'])->name('items.option_pays.update');

        // Route::get('items/{item}/profiles', [ItemProfileController::class, 'edit'])->name('items.profiles.edit');
        // Route::post('items/{item}/profiles', [ItemProfileController::class, 'update'])->name('items.profiles.update');

        // Route::get('items/{item}/icon_items', [ItemIconItemController::class, 'edit'])->name('items.icon_items.edit');
        // Route::post('items/{item}/icon_items', [ItemIconItemController::class, 'update'])->name('items.icon_items.update');

        ////////////////////////////////////////////////////
        // ランキング
        // Route::resource('/rank_cates', RankCateController::class);
        // Route::post('/rank_cates/sort', [RankCateController::class, 'sort'])->name('rank_cates.sort');
        // Route::resource('/ranks', RankController::class);
        // Route::post('/ranks/update-itemrank/{rank_id}', [RankController::class, 'updateItemRank'])->name('ranks.item_rank.update');
        // Route::post('/ranks/sort', [RankController::class, 'sort'])->name('ranks.sort');

        ////////////////////////////////////////////////////
        // 店舗情報
        // Route::resource('/shops', ShopController::class);

        ////////////////////////////////////////////////////
        // コース
        // Route::resource('/course', CourseController::class, [
        //     'names' => 'courses'
        // ]);
        // Route::post('/course/sort', [CourseController::class, 'sort'])->name('courses.sort');

        ////////////////////////////////////////////////////
        // 料金
        // Route::resource('/course_fee', CourseFeeController::class, [
        //     'names' => 'course_fees'
        // ]);
        // Route::post('/course_fee/sort', [CourseFeeController::class, 'sort'])->name('course_fees.sort');

        ////////////////////////////////////////////////////
        // 設備
        // Route::resource('/equipment', EquipmentController::class, [
        //     'names' => 'equipments'
        // ]);
        // Route::post('/equipment/sort', [EquipmentController::class, 'sort'])->name('equipments.sort');

        ////////////////////////////////////////////////////
        // 設備カテゴリ　エリアなど
        // Route::resource('/equipment_cate', EquipmentCateController::class, [
        //     'names' => 'equipment_cates'
        // ]);
        // Route::post('/equipment_cate/sort', [EquipmentCateController::class, 'sort'])->name('equipment_cates.sort');

        ////////////////////////////////////////////////////
        // マイコスチューム
        Route::resource('/mycostume', MycostumeController::class, [
            'names' => 'mycostumes'
        ]);
        Route::post('/mycostume/sort', [MycostumeController::class, 'sort'])->name('mycostumes.sort');

        ////////////////////////////////////////////////////
        // マイオプション
        Route::resource('/myoption', MyoptionController::class, [
            'names' => 'myoptions'
        ]);
        Route::post('/myoption/sort', [MyoptionController::class, 'sort'])->name('myoptions.sort');

        ////////////////////////////////////////////////////
        // マイコスチューム カテゴリ
        // Route::resource('/mycostume_cate', MycostumeCateController::class, [
        //     'names' => 'mycostume_cates'
        // ]);
        // Route::post('/mycostume_cate/sort', [MycostumeCateController::class, 'sort'])->name('mycostume_cates.sort');

        ////////////////////////////////////////////////////
        // 予約管理
        // ※ resource より先に固定パスを定義（/reservation/{id} に吸われないよう）
        Route::get('/reservation/pending-count', function () {
            $count = \App\Models\Reservation::where('flag', 0)
                ->where('created_at', '>=', \Carbon\Carbon::today())
                ->count();

            return response()->json([
                'count' => $count,
                'display' => $count > 5 ? '5+' : $count
            ]);
        })->name('reservations.pendingCount');

        Route::resource('/reservation', ReservationController::class, [
            'names' => 'reservations'
        ]);
        Route::post('/reservation/update-flag', [ReservationController::class, 'updateFlag'])->name('reservations.updateFlag');



        ////////////////////////////////////////////////////
        // 予約履歴管理
        Route::resource('history', HistoryController_old::class, [
            'names' => 'histories'
        ])->only(['index', 'edit', 'update', 'destroy']);

        // Route::post('/history/update-flag', [HistoryController_old::class, 'updateFlag'])->name('histories.updateFlag');



        ////////////////////////////////////////////////////
        // ギャラリー
        Route::resource('/gallery', GalleryController::class);
        Route::post('/gallery/sort', [GalleryController::class, 'sort'])->name('gallery.sort');
        Route::post('gallery/{gallery}/images/upload', [GalleryController::class, 'uploadImages'])->name('gallery.image.upload');
        Route::delete('gallery/images/{image}', [GalleryController::class, 'deleteImage'])->name('gallery.image.delete');
        Route::patch('gallery/images/{image}', [GalleryController::class, 'updateImage'])->name('gallery.image.update');
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






