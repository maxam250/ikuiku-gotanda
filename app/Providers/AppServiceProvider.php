<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Detection\MobileDetect;

use Illuminate\Support\Facades\View; // 各viewへの設定用
use App\Models\Reservation; // 未処理数カウント用　予約
use App\Models\Schedule; // 未処理数カウント用 出勤
use App\Services\ExternalDatabaseService; // 外部DB用サービス
use Carbon\Carbon;
use App\Models\Contact;
use App\Models\Shop;
use App\Services\Payment\DummySquarePaymentGateway;
use App\Services\Payment\PointPurchaseService;
use App\Services\Payment\SquarePaymentGateway;
use App\Services\Payment\SquarePaymentGatewayInterface;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $driver = config('fanclub.payments.driver', 'dummy_square');
        $gatewayClass = $driver === 'square' ? SquarePaymentGateway::class : DummySquarePaymentGateway::class;
        $this->app->bind(SquarePaymentGatewayInterface::class, $gatewayClass);

        // PointPurchaseService は SquarePaymentGatewayInterface を注入
        $this->app->bind(PointPurchaseService::class, function ($app) {
            return new PointPurchaseService($app->make(SquarePaymentGatewayInterface::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // HTMLタグを壊さずに文字数で切り詰めるマクロ
        Str::macro('limitHtml', fn(string $html, int $limit, string $end = '…') =>
            \App\Helpers\HtmlHelper::limitHtml($html, $limit, $end)
        );

        // JSONレスポンスからDebugbarを除外
        if (class_exists('\Barryvdh\Debugbar\Facades\Debugbar')) {
            // リクエストがJSONを期待している場合はDebugbarを無効化
            if (request()->wantsJson() || request()->expectsJson() || request()->isJson() || request()->header('Accept') === 'application/json') {
                \Barryvdh\Debugbar\Facades\Debugbar::disable();
            }
        }

        // if (request()->is('admin/*')) {
        //     config(['session.cookie' => config('session.cookie_admin')]);
        // }

        // //追加
        // Paginator::defaultView('default');
        // Paginator::defaultSimpleView('simple-default');

        // JSONレスポンスには適用しないように、ビューのみに適用
        view()->composer(['layouts.*', 'admin.*', 'item.*', 'member.*', 'index', 'costume', 'gallery', 'schedule', 'item.index', 'studios', 'access', 'contact', 'systems', 'events.*', 'news.*', 'specialEvents.*', 'blogs.*', 'coupons.*', 'reservation.*'], function ($view) {
            $detect = new MobileDetect;
            $view->with('isMobile', $detect->isMobile());
        });


        // 予約の未処理数のカウント 当日以降の未設定
        View::composer(['layouts.*', 'admin.*', 'item.*', 'member.*', 'index', 'costume', 'gallery', 'schedule', 'item.index', 'studios', 'access', 'contact', 'systems', 'events.*', 'news.*', 'specialEvents.*', 'blogs.*', 'coupons.*', 'reservation.*'], function ($view) {
            try {
                $shop = Shop::first();
                if (!$shop) {
                    $view->with('pendingReservationsCount', 0);
                    return;
                }

                // ローカルDBから取得
                // キャッシュを使用してDB負荷を軽減（5分間キャッシュ）
                $cacheKey = 'pending_reservations_count_' . ($shop->id ?? 1);

                $count = \Cache::remember($cacheKey, 300, function() use ($shop) {
                    try {
                        $reservationModel = ExternalDatabaseService::getReservationModel();

                        return $reservationModel::where(function($q) {
                            $q->where('flag', 0)
                            ->orWhereNull('flag');
                        })
                        ->where('shop_id', '=', $shop->id ?? 1)
                        ->where('date', '>=', Carbon::today())
                        ->count();
                    } catch (\Exception $e) {
                        \Log::warning('Failed to count pending reservations', [
                            'error' => $e->getMessage()
                        ]);
                        return 0;
                    }
                });

                $view->with('pendingReservationsCount', $count);
            } catch (\Exception $e) {
                $view->with('pendingReservationsCount', 0);
            }
        });


        // 問い合わせの未処理数のカウント 当日以降の未設定
        View::composer(['layouts.*', 'admin.*', 'item.*', 'member.*', 'index', 'costume', 'gallery', 'schedule', 'item.index', 'studios', 'access', 'contact', 'systems', 'events.*', 'news.*', 'specialEvents.*', 'blogs.*', 'coupons.*', 'reservation.*'], function ($view) {
            try {
                $count = Contact::where('status', 'unread')
                ->count();
                $view->with('pendingContactsCount', $count);
            } catch (\Exception $e) {
                $view->with('pendingContactsCount', 0);
            }
        });



        // 出勤リクエストの未処理数のカウント 当日以降の未設定
        View::composer(['layouts.*', 'admin.*', 'item.*', 'member.*', 'index', 'costume', 'gallery', 'schedule', 'item.index', 'studios', 'access', 'contact', 'systems', 'events.*', 'news.*', 'specialEvents.*', 'blogs.*', 'coupons.*', 'reservation.*'], function ($view) {
            try {
                $count = Schedule::where('flag','!=', 0)
                ->count();
                $view->with('pendingRequestSchCount', $count);
            } catch (\Exception $e) {
                $view->with('pendingRequestSchCount', 0);
            }
        });

        View::composer('*', function ($view) {
            try {
                $view->with('siteSettings', Setting::first());
            } catch (\Exception $e) {
                $view->with('siteSettings', null);
            }
        });
    }
}
