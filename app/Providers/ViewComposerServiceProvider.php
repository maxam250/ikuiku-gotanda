<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Shop;
use App\Models\SeoSetting;
use App\Models\News;
// use App\View\Composers\AutolinkComposer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class ViewComposerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // View::composer('*', function ($view) {
        //     $shops = Shop::first();
        //     $view->with('shops', $shops);
        // });

        //ショップ情報
        View::composer('*', function ($view) {
            try { $shops = Shop::first();
                $view->with('shops', $shops);
            } catch (\Exception $e) {
                 Log::error('Error in ViewComposerServiceProvider: ' . $e->getMessage());
            }
        });

        // SEO設定（フロントのレイアウトのみ）
        View::composer('layouts.app', function ($view) {
            try {
                $routeName = Route::currentRouteName();
                $seoData = $routeName ? SeoSetting::findByRouteKey($routeName) : null;
                $view->with('seoData', $seoData);

                $memberBreadcrumbs = $this->buildMemberAreaBreadcrumbs($routeName);
                if ($memberBreadcrumbs !== null) {
                    $view->with('breadcrumbs', $memberBreadcrumbs);

                    return;
                }

                // パンくずリスト構築（SEO設定ベース）
                $breadcrumbs = [['label' => 'ホーム', 'url' => url('/')]];
                if ($seoData) {
                    // 親ページがあれば追加（最大3階層まで遡る）
                    if ($seoData->breadcrumb_parent_key) {
                        $parent = SeoSetting::findByRouteKey($seoData->breadcrumb_parent_key);
                        if ($parent) {
                            try {
                                $parentUrl = route($parent->page_key);
                            } catch (\Exception $e) {
                                $parentUrl = $parent->canonical_url ?? '#';
                            }
                            $breadcrumbs[] = [
                                'label' => $parent->breadcrumb_label ?: $parent->page_name,
                                'url'   => $parentUrl,
                            ];
                        }
                    }
                    // 現在のページ（末尾はリンクなし）
                    $breadcrumbs[] = [
                        'label' => $seoData->breadcrumb_label ?: $seoData->page_name,
                        'url'   => null,
                    ];
                } else {
                    // SeoSetting未登録ページのフォールバック
                    $fallbackLabel = match ($routeName) {
                        'options'            => 'オプション',
                        'hotels'             => 'ホテル',
                        'x.index'            => 'SNS',
                        'movie.index'        => '動画一覧',
                        'newface.index'      => '新人情報',
                        'fanclub'            => 'ファンクラブ',
                        'fanclub.show'       => 'ファンクラブ',
                        'legal.sct'          => '特定商取引法に基づく表記',
                        'guidance.index'     => 'ご利用ガイド',
                        'specialEvents.index'=> '特設イベント',
                        'contact.confirm'    => 'お問い合わせ内容確認',
                        'contact.complete'   => 'お問い合わせ完了',
                        'reservation.select' => 'ログイン',
                        'reservation.confirm'=> 'ご予約内容の確認',
                        'reservation.thanks' => 'ご予約申し込み完了',
                        default              => null,
                    };
                    if ($fallbackLabel !== null) {
                        $breadcrumbs[] = ['label' => $fallbackLabel, 'url' => null];
                    }
                }
                $view->with('breadcrumbs', $breadcrumbs);
            } catch (\Exception $e) {
                Log::error('Error in SEO ViewComposer: ' . $e->getMessage());
                $view->with('seoData', null);
                $view->with('breadcrumbs', [['label' => 'ホーム', 'url' => url('/')]]);
            }
        });

        // サイドバー用お知らせ（全フロントページ共通）
        // コントローラーがすでに $news を渡している場合（news.index など）は上書きしない
        View::composer('layouts.app', function ($view) {
            try {
                if (!isset($view->getData()['news'])) {
                    $news = News::publiclyVisible()
                        ->orderBy('sort', 'asc')
                        ->orderBy('id', 'desc')
                        ->take(5)
                        ->get();
                    $view->with('news', $news);
                }
            } catch (\Exception $e) {
                Log::error('Error in News ViewComposer: ' . $e->getMessage());
            }
        });

            //AutoLink
        // View::composer('*', AutolinkComposer::class);

    }

    public function register()
    {
        //
    }

    /**
     * 会員エリア（member.*）のパンくず。該当しない場合は null。
     *
     * @return array<int, array{label: string, url: string|null}>|null
     */
    private function buildMemberAreaBreadcrumbs(?string $routeName): ?array
    {
        if (! $routeName || ! str_starts_with($routeName, 'member.')) {
            return null;
        }

        $home = ['label' => 'ホーム', 'url' => url('/')];

        $guestCrumb = match ($routeName) {
            'member.login' => '会員ログイン',
            'member.register' => '会員登録',
            'member.email.sent' => '確認メール送信',
            'member.password.request' => 'パスワード再発行',
            'member.password.reset' => 'パスワード再設定',
            'member.registration.complete' => '登録完了',
            'member.verify.email' => 'メール認証',
            'member.test.verify' => '認証テスト',
            default => null,
        };

        if ($guestCrumb !== null) {
            return [
                $home,
                ['label' => $guestCrumb, 'url' => null],
            ];
        }

        if ($routeName === 'member.dashboard') {
            return [
                $home,
                ['label' => 'マイページ', 'url' => null],
            ];
        }

        $dashboard = ['label' => 'マイページ', 'url' => route('member.dashboard')];

        if ($routeName === 'member.fanclubs.show') {
            $fanclub = request()->route('fanclub');
            $pageLabel = $fanclub?->title ?? 'ファンクラブ';

            return [
                $home,
                $dashboard,
                ['label' => $pageLabel, 'url' => null],
            ];
        }

        if ($routeName === 'member.messages.show') {
            $message = request()->route('message');
            $pageLabel = $message && ! empty($message->title)
                ? $message->title
                : 'メッセージ詳細';

            return [
                $home,
                $dashboard,
                ['label' => $pageLabel, 'url' => null],
            ];
        }

        $pageLabel = match ($routeName) {
            'member.fanclubs.index' => '入会可能ファンクラブ',
            'member.fanclubs.joined' => '入会中ファンクラブ',
            'member.fanclubs.contents' => 'ファンクラブコンテンツ',
            'member.fanclubs.subscribe.confirm' => 'ファンクラブ入会の確認',
            'member.fanclubs.withdraw.confirm' => 'ファンクラブ退会の確認',
            'member.history' => 'ご利用履歴',
            'member.point-history' => 'ポイント履歴',
            'member.account' => 'アカウント',
            'member.account.password' => 'パスワード変更',
            'member.accountUpdate' => 'アカウント',
            'member.messages.index' => 'メッセージ',
            'member.messages.create' => 'メッセージを送る',
            'member.messages.store' => 'メッセージ',
            'member.messages.destroy' => 'メッセージ',
            'member.verification.notice' => 'メールアドレス認証',
            'member.verification.verify' => 'メールアドレス認証',
            'member.verification.send' => '認証メール送信',
            'member.password.confirm' => 'パスワード確認',
            'member.password.update' => 'パスワード変更',
            'member.logout' => 'ログアウト',
            default => str_contains($routeName, 'member.gallery') ? $this->memberGalleryBreadcrumbLabel($routeName) : null,
        };

        if ($pageLabel === null) {
            $pageLabel = 'マイページ';
        }

        return [
            $home,
            $dashboard,
            ['label' => $pageLabel, 'url' => null],
        ];
    }

    private function memberGalleryBreadcrumbLabel(string $routeName): string
    {
        return match (true) {
            str_ends_with($routeName, '.index') => 'マイギャラリー',
            str_ends_with($routeName, '.create') => 'ギャラリー作成',
            str_ends_with($routeName, '.edit') => 'ギャラリー編集',
            str_ends_with($routeName, '.show') => 'ギャラリー詳細',
            default => 'マイギャラリー',
        };
    }
}
