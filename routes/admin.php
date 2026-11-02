<?php

use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Admin\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Admin\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Admin\Auth\NewPasswordController;
use App\Http\Controllers\Admin\Auth\PasswordController;
use App\Http\Controllers\Admin\Auth\PasswordResetLinkController;
use App\Http\Controllers\Admin\Auth\RegisteredUserController;
use App\Http\Controllers\Admin\Auth\VerifyEmailController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;


//未ログイン

Route::middleware('guest:admin')->group(function () {


    Route::get('/', function () {
        return redirect()->route('admin.login');
    });

    // bootstrap/app.phpで既にkanrigamenプレフィックスとadmin.名が設定されているため、ここでは不要
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store'])
        ->middleware(['registration.rate.limit', 'honeypot']);

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


use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\MainVisualController;
// use App\Http\Controllers\ImageController;
use App\Http\Controllers\Admin\ItemImageController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\ScheduleMonthController;
use App\Http\Controllers\Admin\RequestScheduleController;
use App\Http\Controllers\Admin\PickupController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\LinkController;
use App\Http\Controllers\Admin\OptionController;
use App\Http\Controllers\Admin\OptionPayController;
use App\Http\Controllers\Admin\OptionCateController;
use App\Http\Controllers\Admin\ItemOptionController;
use App\Http\Controllers\Admin\ItemOptionPayController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\IconItemController;
use App\Http\Controllers\Admin\ItemIconItemController;
use App\Http\Controllers\Admin\ItemProfileController;
use App\Http\Controllers\Admin\RankCateController;
use App\Http\Controllers\Admin\RankController;
use App\Http\Controllers\Admin\ItemRankController;
use App\Http\Controllers\Admin\CostumeController;
use App\Http\Controllers\Admin\CostumeCateController;
use App\Http\Controllers\Admin\ShopController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CourseFeeController;
use App\Http\Controllers\Admin\EquipmentCateController;
use App\Http\Controllers\Admin\EquipmentController;
use App\Http\Controllers\Admin\ReservationController_old;
use App\Http\Controllers\Admin\HistoryController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\MemberPointController;
use App\Http\Controllers\Admin\MemberHistoryController;
use App\Http\Controllers\Admin\GalleryController;
use App\Http\Controllers\Admin\MyoptionController_old;
use App\Http\Controllers\Admin\MyoptionCateController;
use App\Http\Controllers\Admin\MycostumeController_old;
use App\Http\Controllers\Admin\MycostumeCateController;
use App\Http\Controllers\Admin\MessageController;
use App\Http\Controllers\Admin\MessageTemplateController;
use App\Http\Controllers\Admin\MessageItemController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\MycostumeController;
use App\Http\Controllers\Admin\NewsletterSubscriberController;
use App\Http\Controllers\Admin\NewsletterTemplateController;
use App\Http\Controllers\Admin\NewsletterCampaignController;
use App\Http\Controllers\Admin\MyoptionController;
use App\Http\Controllers\Admin\InterviewController;
use App\Http\Controllers\Admin\PremiumRankController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\SpecialEventController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\OfficeController;
use App\Http\Controllers\Admin\AdminManagementController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\SeoSettingController;
use App\Http\Controllers\Admin\FanclubController;
use App\Http\Controllers\Admin\FanclubContentController;
use App\Http\Controllers\Admin\FanclubContentChildController;
use App\Http\Controllers\Admin\FanclubMembershipController;
use App\Http\Controllers\Admin\FanclubMessageController;
use App\Http\Controllers\Admin\ItemCategoryController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\StaffSelectController;
use App\Http\Controllers\Admin\StaffScheduleController;
use App\Http\Controllers\Admin\SharedMemoController;
use App\Http\Controllers\Admin\SharedMemoCategoryController;
use App\Http\Controllers\Admin\NewfaceController;
use App\Http\Controllers\Admin\ShopGroupController;
use App\Http\Controllers\Admin\NoteController;
use App\Http\Controllers\Admin\AccessLogController;



Route::middleware('auth:admin')->group(function () {
    // Route::get('/admin', function () {
    //     return redirect('/admin/dashboard');
    // })->middleware('admin');

    Route::get('/', function () {
        return redirect('/kanrigamen/dashboard');
    });

    // bootstrap/app.phpで既にkanrigamenプレフィックスとadmin.名が設定されているため、ここでは不要
    // Route::get('/', function () {
    //     return redirect()->route('admin.dashboard');
    // });

        Route::get('dashboard', [DashboardController::class, 'index'])
            ->name('dashboard');
        Route::post('dashboard/mark-read', [DashboardController::class, 'markNotificationAsRead'])
            ->name('dashboard.mark-read');

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
        Route::post('/items/{item_id}/upload-image', [ItemImageController::class, 'uploadImage'])->name('items.upload-image');

        //画像の更新
        Route::post('/items/images/{imageId}/replace', [ItemImageController::class, 'replaceImage']);

        //画像の削除
        Route::delete('/items/images/{imageId}', [ItemImageController::class, 'deleteImage']);

        // 画像のソート
        Route::post('/items/{item_id}/sort-image', [ItemImageController::class, 'sortImage'])->name('items.sort-image');

        // サムネイルの設定
        Route::post('/items/{item_id}/set-thumbnail', [ItemImageController::class, 'setThumbnail']);


        Route::post('/items/{imageId}/replace-image', [ItemImageController::class, 'replaceImage'])->name('items.replace-image');

        //キャスト動画アップロード用
        Route::post('/items/{item_id}/upload-video', [ItemImageController::class, 'uploadVideo'])->name('items.upload-video');

        //キャスト動画削除用
        Route::delete('/items/{item_id}/delete-video', [ItemImageController::class, 'deleteVideo'])->name('items.delete-video');

        //サムネイル動画アップロード用
        Route::post('/items/{item_id}/upload-thumbnail-video', [ItemImageController::class, 'uploadThumbnailVideo'])->name('items.upload-thumbnail-video');

        //サムネイル動画削除用
        Route::delete('/items/{item_id}/delete-thumbnail-video', [ItemImageController::class, 'deleteThumbnailVideo'])->name('items.delete-thumbnail-video');


        ////////////////////////////////////////////////////
        // キャストカテゴリ管理
        Route::resource('item-categories', ItemCategoryController::class)
            ->except(['create', 'show']);
        Route::post('/item-categories/sort', [ItemCategoryController::class, 'sort'])
            ->name('item-categories.sort');

        ////////////////////////////////////////////////////
        // キャスト管理
        Route::post('/items/bulk-action', [ItemController::class, 'bulkAction'])->name('items.bulkAction');
        Route::post('/items/sort', [ItemController::class, 'sort'])->name('items.sort');
        Route::post('/items/{item}/toggle-active', [ItemController::class, 'toggleActive'])->name('items.toggleActive');
        Route::resource('items', ItemController::class);

        ////////////////////////////////////////////////////
        // 新人情報管理
        Route::get('/newfaces', [NewfaceController::class, 'index'])->name('newfaces.index');
        Route::post('/newfaces/bulk-update', [NewfaceController::class, 'bulkUpdate'])->name('newfaces.bulkUpdate');
        Route::post('/newfaces/sort', [NewfaceController::class, 'sort'])->name('newfaces.sort');
        Route::post('/newfaces/bulk-action', [NewfaceController::class, 'bulkAction'])->name('newfaces.bulkAction');
        Route::delete('/newfaces/{id}', [NewfaceController::class, 'destroy'])->name('newfaces.destroy');

        ////////////////////////////////////////////////////
        // メインビジュアル管理
        Route::resource('main-visuals', MainVisualController::class);
        Route::post('/main-visuals/sort', [MainVisualController::class, 'sort'])->name('main-visuals.sort');

        //プロフィール項目
        Route::post('/items/profile', [ItemController::class, 'profile'])->name('items.profile');
        Route::patch('/items/{id}/update-profile', [ItemController::class, 'updateProfile'])->name('items.updateProfile');

        //Q&A項目
        Route::patch('/items/{id}/update-interview', [ItemController::class, 'updateInterview'])->name('items.updateInterview');

        // キャストのマイオプションとマイコスチュームを取得
        Route::get('/cast-items/{itemId}', [ReservationController_old::class, 'getCastItems'])->name('cast-items');

        // OptionをAjaxで更新するルート
        Route::post('/admin/items/{id}/update-option', [ItemController::class, 'updateOption'])->name('items.updateOption');

        // OptionPayをAjaxで更新するルート
        Route::post('/admin/items/{id}/update-option-pay', [ItemController::class, 'updateOptionPay'])->name('items.updateOptionPay');

        // IconItemをAjaxで更新するルート
        Route::post('/admin/items/{id}/update-icon-item', [ItemController::class, 'updateIconItem'])->name('items.updateIconItem');

        // コース設定をAjaxで更新するルート
        Route::post('/admin/items/{id}/update-course', [ItemController::class, 'updateCourse'])->name('items.updateCourse');

        // 料金設定をAjaxで更新するルート
        Route::post('/admin/items/{id}/update-course-fee', [ItemController::class, 'updateCourseFee'])->name('items.updateCourseFee');

        // クーポン設定をAjaxで更新するルート
        Route::post('/items/{id}/update-coupons', [ItemController::class, 'updateCoupons'])->name('items.updateCoupons');

        // ログイン情報を更新するルート
        Route::patch('/items/{id}/update-login', [ItemController::class, 'updateLogin'])->name('items.updateLogin');



        ////////////////////////////////////////////////////
        // スケジュール管理
        Route::get('/schedules/{date?}', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::put('/schedules/{id}/status', [ScheduleController::class, 'updateStatus'])->name('schedules.updateStatus');
        Route::put('/schedules/{id}/update-time', [ScheduleController::class, 'updateTime'])->name('schedules.updateTime');
        Route::post('/schedules/bulk-update/{date?}', [ScheduleController::class, 'bulkUpdate'])->name('schedules.bulkUpdate');
        Route::post('/schedules/sort', [scheduleController::class, 'sort'])->name('schedules.sort');
        Route::delete('/schedules/{id}', [scheduleController::class, 'destroy'])->name('schedules.destroy');

        ////////////////////////////////////////////////////
        // 月間スケジュール管理
        Route::get('/schedules_month/{itemId?}/{year?}/{month?}', [ScheduleMonthController::class, 'index'])->name('schedules_month.index');
        // Route::get('/schedules_month/{itemId}', [ScheduleMonthController::class, 'index'])->name('schedules.index');
        Route::post('/schedules_month/toggle', [ScheduleMonthController::class, 'toggleSchedule'])->name('schedules_month.toggle');
        Route::post('/schedules_month/update', [ScheduleMonthController::class, 'updateSchedule'])->name('schedules_month.update');
        Route::delete('/schedules_month/{id}', [ScheduleMonthController::class, 'destroy'])->name('schedules_month.destroy');

       ////////////////////////////////////////////////////
        // スケジュールリクエスト管理
        Route::get('request_schedules', [RequestScheduleController::class, 'index'])->name('request_schedules.index');
        Route::post('request_schedules/approve', [RequestScheduleController::class, 'approve'])->name('request_schedules.approve');
        Route::post('request_schedules/approve_bulk', [RequestScheduleController::class, 'approveBulk'])->name('request_schedules.approve_bulk');
        Route::post('request_schedules/reject', [RequestScheduleController::class, 'reject'])->name('request_schedules.reject');

        ////////////////////////////////////////////////////
        // ピックアップ
        Route::get('/pickups', [PickupController::class, 'index'])->name('pickups.index');
        Route::post('/pickups/bulk-update', [PickupController::class, 'bulkUpdate'])->name('pickups.bulkUpdate');
        Route::post('/pickups/sort', [PickupController::class, 'sort'])->name('pickups.sort');
        Route::delete('/pickups/{id}', [PickupController::class, 'destroy'])->name('pickups.destroy');


        ////////////////////////////////////////////////////
        // イベント
        Route::resource('events', EventController::class);
        Route::post('/events/sort', [EventController::class, 'sort'])->name('events.sort');


        /*
        GET /admin/events（index）: イベントの一覧を表示
        GET /admin/events/create（create）: 新しいイベントの作成フォーム
        POST /admin/events（store）: 新しいイベントを保存
        GET /admin/events/{event}（show）: 特定のイベントを表示
        GET /admin/events/{event}/edit（edit）: 特定のイベントを編集するフォーム
        PUT/PATCH /admin/events/{event}（update）: 特定のイベントを更新
        DELETE /admin/events/{event}（destroy）: 特定のイベントを削除
        */

        ////////////////////////////////////////////////////
        // ジャーナル/ブログ
        Route::resource('blogs', BlogController::class);
        Route::delete('admin/blogs/{blog}/main-image', [BlogController::class, 'deleteMainImage'])->name('blogs.deleteMainImage');
        Route::delete('admin/blogs/{blog}/sub-image/{index}', [BlogController::class, 'deleteSubImage'])->name('blogs.deleteSubImage');

        ////////////////////////////////////////////////////
        // ニュース/インフォメーション
        Route::resource('/news', NewsController::class);
        Route::post('/news/sort', [NewsController::class, 'sort'])->name('news.sort');

        ////////////////////////////////////////////////////
        // リンク
        Route::resource('/links', LinkController::class);
        Route::post('/links/sort', [LinkController::class, 'sort'])->name('links.sort');

        ////////////////////////////////////////////////////
        // プロフィール
        Route::resource('/profiles', ProfileController::class);
        Route::post('/profiles/sort', [ProfileController::class, 'sort'])->name('profiles.sort');

        ////////////////////////////////////////////////////
        // Q&A管理
        Route::resource('/interviews', InterviewController::class);
        Route::post('/interviews/sort', [InterviewController::class, 'sort'])->name('interviews.sort');

        ////////////////////////////////////////////////////
        // プレミアランク管理
        Route::resource('/premium-ranks', PremiumRankController::class)->except(['create', 'show']);
        Route::post('/premium-ranks/sort', [PremiumRankController::class, 'sort'])->name('premium-ranks.sort');

        ////////////////////////////////////////////////////
        // キャストアイコン
        Route::resource('/icon_items', IconItemController::class);
        Route::post('/icon_items/sort', [IconItemController::class, 'sort'])->name('icon_items.sort');

        ////////////////////////////////////////////////////
        // オプション
        Route::resource('/options', OptionController::class);
        Route::post('/options/sort', [OptionController::class, 'sort'])->name('options.sort');

        ////////////////////////////////////////////////////
        // オプションPAY
        Route::resource('/option_pays', OptionPayController::class);
        Route::post('/option_pays/sort', [OptionPayController::class, 'sort'])->name('option_pays.sort');

        ////////////////////////////////////////////////////
        // オプションカテゴリ
        Route::resource('/option_cates', OptionCateController::class);
        Route::post('/option_cates/sort', [OptionCateController::class, 'sort'])->name('option_cates.sort');

        ////////////////////////////////////////////////////
        // キャストに対してオプション等の設定
        Route::get('items/{item}/options', [ItemOptionController::class, 'edit'])->name('items.options.edit');
        Route::post('items/{item}/options', [ItemOptionController::class, 'update'])->name('items.options.update');

        Route::get('items/{item}/option_pays', [ItemOptionPayController::class, 'edit'])->name('items.option_pays.edit');
        Route::post('items/{item}/option_pays', [ItemOptionPayController::class, 'update'])->name('items.option_pays.update');

        Route::get('items/{item}/profiles', [ItemProfileController::class, 'edit'])->name('items.profiles.edit');
        Route::post('items/{item}/profiles', [ItemProfileController::class, 'update'])->name('items.profiles.update');

        Route::get('items/{item}/icon_items', [ItemIconItemController::class, 'edit'])->name('items.icon_items.edit');
        Route::post('items/{item}/icon_items', [ItemIconItemController::class, 'update'])->name('items.icon_items.update');

        ////////////////////////////////////////////////////
        // ランキング
        Route::resource('/rank_cates', RankCateController::class);
        Route::post('/rank_cates/sort', [RankCateController::class, 'sort'])->name('rank_cates.sort');
        Route::resource('/ranks', RankController::class);
        Route::post('/ranks/update-itemrank/{rank_id}', [RankController::class, 'updateItemRank'])->name('ranks.item_rank.update');
        Route::post('/ranks/sort', [RankController::class, 'sort'])->name('ranks.sort');

        ////////////////////////////////////////////////////
        // 店舗情報
        Route::resource('/shops', ShopController::class);

        ////////////////////////////////////////////////////
        // 系列店管理
        Route::post('/shop-groups/sort', [ShopGroupController::class, 'sort'])->name('shop-groups.sort');
        Route::resource('/shop-groups', ShopGroupController::class)->except(['show']);

        ////////////////////////////////////////////////////
        // コース
        Route::resource('/course', CourseController::class, [
            'names' => 'courses'
        ]);
        Route::post('/course/sort', [CourseController::class, 'sort'])->name('courses.sort');

        ////////////////////////////////////////////////////
        // 料金
        Route::resource('/course_fee', CourseFeeController::class, [
            'names' => 'course_fees'
        ]);
        Route::post('/course_fee/sort', [CourseFeeController::class, 'sort'])->name('course_fees.sort');

        ////////////////////////////////////////////////////
        // 設備
        Route::resource('/equipment', EquipmentController::class, [
            'names' => 'equipments'
        ]);
        Route::post('/equipment/sort', [EquipmentController::class, 'sort'])->name('equipments.sort');

        // ホテルサブ画像管理
        Route::post('/equipment/{equipment}/equipment-sub-image', [EquipmentController::class, 'addEquipmentSubImage'])->name('equipments.addEquipmentSubImage');
        Route::delete('/equipment/equipment-sub-image/{image}', [EquipmentController::class, 'deleteEquipmentSubImage'])->name('equipments.deleteEquipmentSubImage');
        Route::post('/equipment/{equipment}/equipment-list-image', [EquipmentController::class, 'addEquipmentListImage'])->name('equipments.addEquipmentListImage');
        Route::delete('/equipment/equipment-list-image/{image}', [EquipmentController::class, 'deleteEquipmentListImage'])->name('equipments.deleteEquipmentListImage');
        Route::post('/equipment/{equipment}/accessory-list-image', [EquipmentController::class, 'addAccessoryListImage'])->name('equipments.addAccessoryListImage');
        Route::delete('/equipment/accessory-list-image/{image}', [EquipmentController::class, 'deleteAccessoryListImage'])->name('equipments.deleteAccessoryListImage');

        ////////////////////////////////////////////////////
        // 設備カテゴリ　エリアなど
        Route::resource('/equipment_cate', EquipmentCateController::class, [
            'names' => 'equipment_cates'
        ]);
        Route::post('/equipment_cate/sort', [EquipmentCateController::class, 'sort'])->name('equipment_cates.sort');

        ////////////////////////////////////////////////////
        // コスチューム
        Route::resource('/costume', CostumeController::class, [
            'names' => 'costumes'
        ]);
        Route::post('/costume/sort', [CostumeController::class, 'sort'])->name('costumes.sort');

        ////////////////////////////////////////////////////
        // コスチューム カテゴリ
        Route::resource('/costume_cate', CostumeCateController::class, [
            'names' => 'costume_cates'
        ]);
        Route::post('/costume_cate/sort', [CostumeCateController::class, 'sort'])->name('costume_cates.sort');

        ////////////////////////////////////////////////////
        // 予約管理
        Route::prefix('reservation')->name('reservations.')->group(function () {
            Route::get('/', [ReservationController_old::class, 'index'])->name('index');
            Route::get('/print', [ReservationController_old::class, 'printView'])->name('print');
            Route::get('/create', [ReservationController_old::class, 'create'])->name('create');
            Route::post('/', [ReservationController_old::class, 'store'])->name('store');
            Route::post('/update-flag', [ReservationController_old::class, 'updateFlag'])->name('updateFlag');
            Route::get('/pending-count', [ReservationController_old::class, 'getPendingCount'])->name('pendingCount');

           // 予約関連のルート
            Route::get('/recent', [ReservationController_old::class, 'recent'])
            ->name('recent');

            // 料金計算API
            Route::post('/calculate-fees', [ReservationController_old::class, 'calculateFees'])->name('calculateFees');

            // 特定の予約IDに対するルート（より具体的なルートを先に定義）
            Route::get('/{reservation}/edit', [ReservationController_old::class, 'edit'])->name('edit');
            Route::put('/{reservation}', [ReservationController_old::class, 'update'])->name('update');
            Route::delete('/{reservation}', [ReservationController_old::class, 'destroy'])->name('destroy');
            Route::get('/{reservation}/details', [ReservationController_old::class, 'details'])->name('details');
            Route::get('/{reservation}', [ReservationController_old::class, 'show'])->name('show');
        });



        ////////////////////////////////////////////////////
        // 予約履歴
        Route::get('/history', [HistoryController::class, 'index'])->name('histories.index');
        Route::get('/history/{id}', [HistoryController::class, 'show'])->name('histories.show');
        Route::delete('/history/{id}', [HistoryController::class, 'destroy'])->name('histories.destroy');
        Route::post('/history/update-flag', [HistoryController::class, 'updateFlag'])->name('histories.updateFlag');

        // 予約履歴編集用ルート
        Route::get('/history/{id}/edit', [HistoryController::class, 'edit'])->name('histories.edit');
        Route::put('/history/{id}', [HistoryController::class, 'update'])->name('histories.update');

        ////////////////////////////////////////////////////
        // 会員管理
        Route::resource('/members', MemberController::class, [
            'names' => 'members'
        ]);
        Route::post('/members/update-flag', [MemberController::class, 'updateFlag'])->name('members.updateFlag');

        ////////////////////////////////////////////////////
        // 会員ポイント管理
        Route::prefix('member_points')->name('member_points.')->middleware(['auth:admin'])->group(function () {
            Route::get('/', [MemberPointController::class, 'index'])->name('index');
            Route::get('/{member}/edit', [MemberPointController::class, 'edit'])->name('edit');
            Route::put('/{member}', [MemberPointController::class, 'update'])->name('update');
            Route::get('/suggest', [MemberPointController::class, 'suggest'])->name('suggest');
            Route::post('/{member}/store', [\App\Http\Controllers\Admin\MemberPointController::class, 'store'])->name('store');
            Route::put('/history/{history}/update', [\App\Http\Controllers\Admin\MemberPointController::class, 'update'])->name('history.update');
            Route::delete('/history/{history}/destroy', [\App\Http\Controllers\Admin\MemberPointController::class, 'destroy'])->name('history.destroy');
        });
        ////////////////////////////////////////////////////
        // 会員利用履歴管理
        Route::prefix('members/{member}/histories')->name('member_histories.')->group(function () {
            Route::get('/', [MemberHistoryController::class, 'index'])->name('index');
            Route::get('/create', [MemberHistoryController::class, 'create'])->name('create');
            Route::post('/', [MemberHistoryController::class, 'store'])->name('store');
            Route::get('/{history}/edit', [MemberHistoryController::class, 'edit'])->name('edit');
            Route::put('/{history}', [MemberHistoryController::class, 'update'])->name('update');
            Route::get('/export', [MemberHistoryController::class, 'export'])->name('export');
        });

        ////////////////////////////////////////////////////
        // ギャラリー
        Route::resource('/gallery', GalleryController::class);
        Route::post('/gallery/sort', [GalleryController::class, 'sort'])->name('gallery.sort');
        Route::post('gallery/{gallery}/images/upload', [GalleryController::class, 'uploadImages'])->name('gallery.image.upload');
        Route::delete('gallery/{galleryId}/images/{imageId}', [GalleryController::class, 'deleteImage'])->name('gallery.image.delete');
        Route::patch('gallery/images/{image}', [GalleryController::class, 'updateImage'])->name('gallery.image.update');
        Route::post('gallery/{gallery}/images/sort', [GalleryController::class, 'sortImages'])->name('gallery.image.sort');
        Route::post('gallery/{gallery}/video/upload', [GalleryController::class, 'uploadVideo'])->name('gallery.video.upload');
        Route::delete('gallery/{gallery}/video', [GalleryController::class, 'deleteVideo'])->name('gallery.video.delete');
        Route::post('gallery/{gallery}/video-card/upload', [GalleryController::class, 'uploadVideoCard'])->name('gallery.video-card.upload');

        ////////////////////////////////////////////////////
        // マイコスチューム（旧バージョン - 非推奨）
        // Route::resource('/mycostume', MycostumeController_old::class, [
        //     'names' => 'mycostumes_old'
        // ]);
        // Route::post('/mycostume/sort', [MycostumeController_old::class, 'sort'])->name('mycostumes_old.sort');

        // マイコスチューム管理（キャスト別表示）
        Route::resource('/mycostumes', MycostumeController::class);
        Route::post('/mycostumes/sort', [MycostumeController::class, 'sort'])->name('mycostumes.sort');
        Route::get('/mycostumes/cast/{itemId}', [MycostumeController::class, 'cast'])->name('mycostumes.cast');

        ////////////////////////////////////////////////////
        // マイオプション（旧バージョン - 非推奨）
        // Route::resource('/myoption', MyoptionController_old::class, [
        //     'names' => 'myoptions_old'
        // ]);
        // Route::post('/myoption/sort', [MyoptionController_old::class, 'sort'])->name('myoptions_old.sort');

        // マイオプション管理（キャスト別表示）
        Route::resource('/myoptions', MyoptionController::class);
        Route::post('/myoptions/sort', [MyoptionController::class, 'sort'])->name('myoptions.sort');
        Route::get('/myoptions/cast/{itemId}', [MyoptionController::class, 'cast'])->name('myoptions.cast');

        // キャスト編集ページ インライン マイコスチューム管理
        Route::post('/items/{item}/mycostumes', [ItemController::class, 'storeMycostume'])->name('items.mycostumes.store');
        Route::post('/items/{item}/mycostumes/{mycostume}/update', [ItemController::class, 'updateMycostume'])->name('items.mycostumes.update');
        Route::delete('/items/{item}/mycostumes/{mycostume}', [ItemController::class, 'destroyMycostume'])->name('items.mycostumes.destroy');
        Route::post('/items/{item}/mycostumes/{mycostume}/toggle', [ItemController::class, 'toggleMycostumeActive'])->name('items.mycostumes.toggle');

        // キャスト編集ページ インライン マイオプション管理
        Route::post('/items/{item}/myoptions', [ItemController::class, 'storeMyoption'])->name('items.myoptions.store');
        Route::post('/items/{item}/myoptions/{myoption}/update', [ItemController::class, 'updateMyoption'])->name('items.myoptions.update');
        Route::delete('/items/{item}/myoptions/{myoption}', [ItemController::class, 'destroyMyoption'])->name('items.myoptions.destroy');
        Route::post('/items/{item}/myoptions/{myoption}/toggle', [ItemController::class, 'toggleMyoptionActive'])->name('items.myoptions.toggle');

        ////////////////////////////////////////////////////
        // マイオプション カテゴリ
        Route::resource('/myoption_cates', MyoptionCateController::class, [
            'names' => 'myoption_cates'
        ]);
        Route::post('/myoption_cate/sort', [MyoptionCateController::class, 'sort'])->name('myoption_cates.sort');

        ////////////////////////////////////////////////////
        // メッセージ　会員
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/', [MessageController::class, 'index'])->name('index');
            Route::get('/unread-count', [MessageController::class, 'getUnreadCount'])->name('unread-count');
            Route::get('/{member}', [MessageController::class, 'show'])->name('show');
            Route::post('/', [MessageController::class, 'store'])->name('store');
            Route::post('/apply-template', [MessageController::class, 'applyTemplate'])->name('apply-template');
            Route::post('/{message}/read', [MessageController::class, 'markAsRead'])->name('read');

            // 非会員メッセージ用ルート
            Route::get('/guest/{email}', [MessageController::class, 'showGuest'])->name('show.guest');
            Route::post('/guest', [MessageController::class, 'storeGuest'])->name('store.guest');
        });

        ////////////////////////////////////////////////////
        // メッセージ　キャスト
        Route::prefix('message_item')->name('message_item.')->group(function () {
            Route::get('/', [MessageItemController::class, 'index'])->name('index');
            Route::get('/unread-count', [MessageItemController::class, 'getUnreadCount'])->name('unread-count');
            Route::get('/{member}', [MessageItemController::class, 'show'])->name('show');
            Route::post('/', [MessageItemController::class, 'store'])->name('store');
            Route::post('/apply-template', [MessageItemController::class, 'applyTemplate'])->name('apply-template');
            Route::post('/{message}/read', [MessageItemController::class, 'markAsRead'])->name('read');
        });

        ////////////////////////////////////////////////////
        // メッセージテンプレート
        Route::prefix('message-templates')->name('message-templates.')->group(function () {
            Route::get('/', [MessageTemplateController::class, 'index'])->name('index');
            Route::get('/create', [MessageTemplateController::class, 'create'])->name('create');
            Route::post('/', [MessageTemplateController::class, 'store'])->name('store');
            Route::get('/{template}/edit', [MessageTemplateController::class, 'edit'])->name('edit');
            Route::put('/{template}', [MessageTemplateController::class, 'update'])->name('update');
            Route::delete('/{template}', [MessageTemplateController::class, 'destroy'])->name('destroy');
            Route::get('/{template}/preview', [MessageTemplateController::class, 'preview'])->name('preview');
        });

        // 予約情報API
        Route::get('/reservations/future', [ReservationController_old::class, 'getFutureReservations'])->name('reservations.future');
        Route::get('/reservations/{id}/details', [ReservationController_old::class, 'getReservationDetails'])->name('reservations.get-details');

        ////////////////////////////////////////////////////
        // 問い合わせ管理
        Route::prefix('contacts')->name('contacts.')->group(function () {
            Route::get('/', [ContactController::class, 'index'])->name('index');
            Route::get('/{contact}', [ContactController::class, 'show'])->name('show');
            Route::get('/{contact}/edit', [ContactController::class, 'edit'])->name('edit');
            Route::put('/{contact}', [ContactController::class, 'update'])->name('update');
            Route::delete('/{contact}', [ContactController::class, 'destroy'])->name('destroy');
            Route::get('/unread-count', [ContactController::class, 'getUnreadCount'])->name('unread-count');
            Route::get('/spam-count', [ContactController::class, 'getSpamCount'])->name('spam-count');
        });

        ////////////////////////////////////////////////////
        // マイコスチューム カテゴリ
        Route::resource('/mycostume_cates', MycostumeCateController::class, [
            'names' => 'mycostume_cates'
        ]);
        Route::post('/mycostume_cate/sort', [MycostumeCateController::class, 'sort'])->name('mycostume_cates.sort');

        ////////////////////////////////////////////////////
        // クーポン管理
        Route::resource('coupons', CouponController::class);
        Route::post('/coupons/{coupon}/toggle-status', [CouponController::class, 'toggleStatus'])->name('coupons.toggle-status');


        // 事務所管理（CRUD）
        Route::resource('offices', OfficeController::class);

        // スタッフ管理（CRUD）
        Route::resource('staff', StaffController::class);

        ////////////////////////////////////////////////////
        // スタッフ選択セッション（ヘッダー）
        Route::prefix('staff-api')->name('staff-api.')->group(function () {
            Route::get('/list',         [StaffSelectController::class, 'getStaffList'])->name('list');
            Route::post('/set-session', [StaffSelectController::class, 'setStaffSession'])->name('set-session');
            Route::get('/current',      [StaffSelectController::class, 'getCurrentStaff'])->name('current');
        });

        ////////////////////////////////////////////////////
        // スタッフスケジュール管理
        Route::prefix('staff-schedule')->name('staff_schedule.')->group(function () {
            Route::get('/',             [StaffScheduleController::class, 'index'])->name('index');
            Route::post('/store',       [StaffScheduleController::class, 'store'])->name('store');
            Route::post('/bulk-store',  [StaffScheduleController::class, 'bulkStore'])->name('bulk-store');
            Route::get('/get-schedule', [StaffScheduleController::class, 'getSchedule'])->name('get-schedule');
            Route::post('/destroy',     [StaffScheduleController::class, 'destroy'])->name('destroy');
        });


        ////////////////////////////////////////////////////
        // 共有メモ管理
        Route::resource('shared-memos', SharedMemoController::class)->names('shared_memos');
        Route::resource('shared-memo-categories', SharedMemoCategoryController::class)->names('shared_memo_categories');

        ////////////////////////////////////////////////////
        // 特設イベント管理
        Route::resource('special-events', SpecialEventController::class);
        Route::post('/special-events/{specialEvent}/toggle-status', [SpecialEventController::class, 'toggleStatus'])->name('special-events.toggle-status');

        ////////////////////////////////////////////////////
        // 管理者管理（全管理者アクセス可・スーパー管理者情報はコントローラーで制御）
        Route::prefix('admins')->name('admins.')->group(function () {
            Route::get('/', [AdminManagementController::class, 'index'])->name('index');
            Route::get('/create', [AdminManagementController::class, 'create'])->name('create');
            Route::post('/', [AdminManagementController::class, 'store'])->name('store');
            Route::get('/{admin}/edit', [AdminManagementController::class, 'edit'])->name('edit');
            Route::put('/{admin}', [AdminManagementController::class, 'update'])->name('update');
            Route::delete('/{admin}', [AdminManagementController::class, 'destroy'])->name('destroy');
        });

        ////////////////////////////////////////////////////
        // サーバー設定
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ServerSettingsController::class, 'index'])->name('index');
            Route::post('/test-connection', [\App\Http\Controllers\Admin\ServerSettingsController::class, 'testExternalConnection'])->name('test-connection');
            Route::post('/enable', [\App\Http\Controllers\Admin\ServerSettingsController::class, 'enableExternal'])->name('enable');
            Route::post('/disable', [\App\Http\Controllers\Admin\ServerSettingsController::class, 'disableExternal'])->name('disable');
            Route::post('/update', [\App\Http\Controllers\Admin\ServerSettingsController::class, 'updateSettings'])->name('update');
            Route::get('/status', [\App\Http\Controllers\Admin\ServerSettingsController::class, 'getConnectionStatus'])->name('status');
        });

        Route::get('site-settings', [SiteSettingController::class, 'edit'])->name('site-settings.edit');
        Route::put('site-settings', [SiteSettingController::class, 'update'])->name('site-settings.update');

        ////////////////////////////////////////////////////
        // SEO設定
        Route::resource('seo-settings', SeoSettingController::class)->only(['index', 'store', 'edit', 'update', 'destroy']);

        // ファンクラブ管理
        Route::resource('fanclubs', FanclubController::class)->names('fanclubs');

        // ファンクラブ会員管理
        Route::get('fanclub-memberships', [FanclubMembershipController::class, 'index'])->name('fanclubs.memberships.index');
        Route::get('fanclub-memberships/{membership}', [FanclubMembershipController::class, 'show'])->name('fanclubs.memberships.show');
        Route::patch('fanclub-memberships/{membership}', [FanclubMembershipController::class, 'update'])->name('fanclubs.memberships.update');
        Route::post('fanclub-memberships/{membership}/warn', [FanclubMembershipController::class, 'warn'])->name('fanclubs.memberships.warn');
        Route::post('fanclub-memberships/{membership}/suspend', [FanclubMembershipController::class, 'suspend'])->name('fanclubs.memberships.suspend');
        Route::post('fanclub-memberships/{membership}/expel', [FanclubMembershipController::class, 'expel'])->name('fanclubs.memberships.expel');
        Route::get('fanclub-payment-logs', [FanclubMembershipController::class, 'paymentLogs'])->name('fanclubs.payment_logs.index');
        Route::resource('fanclub-contents', FanclubContentController::class)
            ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'])
            ->names('fanclubs.contents');
        Route::resource('fanclub-contents.children', FanclubContentChildController::class)
            ->except(['show'])
            ->names('fanclubs.contents.children');

        // ファンクラブメッセージ監視
        Route::get('fanclub-messages', [FanclubMessageController::class, 'index'])->name('fanclub-messages.index');
        Route::get('fanclub-messages/{fanclubMessage}', [FanclubMessageController::class, 'show'])->name('fanclub-messages.show');
        Route::patch('fanclub-messages/{fanclubMessage}/warn', [FanclubMessageController::class, 'warn'])->name('fanclub-messages.warn');
        Route::patch('fanclub-messages/{fanclubMessage}/hide', [FanclubMessageController::class, 'hide'])->name('fanclub-messages.hide');
        Route::patch('fanclub-messages/{fanclubMessage}/status', [FanclubMessageController::class, 'updateStatus'])->name('fanclub-messages.status');
        Route::delete('fanclub-messages/{fanclubMessage}', [FanclubMessageController::class, 'destroy'])->name('fanclub-messages.destroy');
        Route::post('fanclub-messages/{fanclubMessage}/suspend', [FanclubMessageController::class, 'suspend'])->name('fanclub-messages.suspend');
        // 禁止ワード管理
        Route::get('fanclub-banned-words', [FanclubMessageController::class, 'bannedWords'])->name('fanclub-banned-words.index');
        Route::post('fanclub-banned-words', [FanclubMessageController::class, 'storeBannedWord'])->name('fanclub-banned-words.store');
        Route::delete('fanclub-banned-words/{fanclubBannedWord}', [FanclubMessageController::class, 'destroyBannedWord'])->name('fanclub-banned-words.destroy');

        ////////////////////////////////////////////////////
        // 注意事項管理
        Route::resource('notes', NoteController::class)->except(['create', 'show']);
        Route::post('/notes/sort', [NoteController::class, 'sort'])->name('notes.sort');

        ////////////////////////////////////////////////////
        // アクセスログ（UU/PV・リファラ）
        // 一時停止中：サーバーDB負荷軽減。後日 LockerStudio 導入予定
        // Route::get('access-logs', [AccessLogController::class, 'index'])->name('access-logs.index');

        ////////////////////////////////////////////////////
        // バックアップ管理
        Route::prefix('backup')->name('backup.')->group(function () {
            Route::get('/', [BackupController::class, 'index'])->name('index');
            Route::post('/create', [BackupController::class, 'create'])->name('create');
            Route::post('/s3-backup', [BackupController::class, 's3Backup'])->name('s3Backup');
            Route::post('/s3-restore', [BackupController::class, 's3Restore'])->name('s3Restore');
            Route::get('/download/{type}/{filename}', [BackupController::class, 'download'])
                ->name('download')
                ->where(['type' => 'images|database']);
            Route::delete('/{type}/{filename}', [BackupController::class, 'destroy'])
                ->name('destroy')
                ->where(['type' => 'images|database']);
        });

        ////////////////////////////////////////////////////
        // メールマガジン管理
        Route::prefix('newsletter')->name('newsletter.')->group(function () {
            // 購読者管理
            Route::prefix('subscribers')->name('subscribers.')->group(function () {
                Route::get('/', [NewsletterSubscriberController::class, 'index'])->name('index');
                Route::get('/create', [NewsletterSubscriberController::class, 'create'])->name('create');
                Route::post('/', [NewsletterSubscriberController::class, 'store'])->name('store');
                Route::post('/bulk-destroy', [NewsletterSubscriberController::class, 'bulkDestroy'])->name('bulk-destroy');
                Route::post('/bulk-unsubscribe', [NewsletterSubscriberController::class, 'bulkUnsubscribe'])->name('bulk-unsubscribe');
                Route::get('/export', [NewsletterSubscriberController::class, 'export'])->name('export');
                Route::get('/{id}', [NewsletterSubscriberController::class, 'show'])->name('show')->where('id', '[0-9]+');
                Route::get('/{id}/edit', [NewsletterSubscriberController::class, 'edit'])->name('edit')->where('id', '[0-9]+');
                Route::put('/{id}', [NewsletterSubscriberController::class, 'update'])->name('update')->where('id', '[0-9]+');
                Route::delete('/{id}', [NewsletterSubscriberController::class, 'destroy'])->name('destroy')->where('id', '[0-9]+');
            });

            // テンプレート管理
            Route::prefix('templates')->name('templates.')->group(function () {
                Route::get('/', [NewsletterTemplateController::class, 'index'])->name('index');
                Route::get('/create', [NewsletterTemplateController::class, 'create'])->name('create');
                Route::post('/', [NewsletterTemplateController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [NewsletterTemplateController::class, 'edit'])->name('edit');
                Route::put('/{id}', [NewsletterTemplateController::class, 'update'])->name('update');
                Route::delete('/{id}', [NewsletterTemplateController::class, 'destroy'])->name('destroy');
                Route::get('/{id}/data', [NewsletterTemplateController::class, 'getData'])->name('data');
                Route::get('/{id}/preview', [NewsletterTemplateController::class, 'preview'])->name('preview');
                Route::post('/{id}/duplicate', [NewsletterTemplateController::class, 'duplicate'])->name('duplicate');
            });

            // キャンペーン管理
            Route::prefix('campaigns')->name('campaigns.')->group(function () {
                Route::get('/', [NewsletterCampaignController::class, 'index'])->name('index');
                Route::get('/create', [NewsletterCampaignController::class, 'create'])->name('create');
                Route::post('/', [NewsletterCampaignController::class, 'store'])->name('store');
                Route::get('/{id}', [NewsletterCampaignController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [NewsletterCampaignController::class, 'edit'])->name('edit');
                Route::put('/{id}', [NewsletterCampaignController::class, 'update'])->name('update');
                Route::delete('/{id}', [NewsletterCampaignController::class, 'destroy'])->name('destroy');
                Route::post('/{id}/send', [NewsletterCampaignController::class, 'send'])->name('send');
                Route::post('/{id}/test-send', [NewsletterCampaignController::class, 'testSend'])->name('test-send');
                Route::post('/{id}/cancel', [NewsletterCampaignController::class, 'cancel'])->name('cancel');
                Route::post('/{id}/reset-sending', [NewsletterCampaignController::class, 'resetSending'])->name('reset-sending');
                Route::post('/process-scheduled', [NewsletterCampaignController::class, 'processScheduled'])->name('process-scheduled');
            });

            // バウンス処理
            Route::post('/process-bounced', [NewsletterSubscriberController::class, 'processBounced'])->name('process-bounced');
        });
});
