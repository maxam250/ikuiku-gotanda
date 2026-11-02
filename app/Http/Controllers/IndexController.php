<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Gallery;
use App\Models\GalleryImage;
use App\Models\User;
use App\Models\Item;
use App\Models\ItemImage;
use App\Models\News;
use App\Models\Event;
use App\Models\MainVisual;
use App\Models\Schedule;
use App\Models\Equipment;
use App\Models\Shop;
use App\Models\Reservation;
use App\Models\ItemCategory;
use App\Models\Link;
use App\Models\SpecialEvent;
use App\Models\FanclubContent;
use App\Models\Rank;
use App\Models\RankCate;
use App\Models\Pickup;
use App\Models\Newface;
use App\Models\Option;
use App\Models\OptionCate;
use App\Models\OptionPay;
use Carbon\Carbon;

class IndexController extends Controller
{
    //
    public function index(Request $request)
    {


        $items = Item::select('items.*')
            ->addSelect([
                'filename' => ItemImage::select('filename')
                    ->whereColumn('item_id', 'items.id')
                    ->thumbnailOrder() // チェック済み → 無ければ先頭の画像
                    ->limit(1)
            ])
            ->where('items.item_active', 1)
            ->orderBy('items.item_sort')
            ->get();


        $news = News::publiclyVisible()
            ->orderBy('sort', 'asc')
            ->orderBy('id', 'desc')
            ->limit(8)
            ->get();


        $events = Event::whereIn('position', [1, 2])
            ->where(function ($query) {
                $query->where('timer_flag', 0)
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('timer_flag', 1)
                                ->where('date_start', '<=', Carbon::now())
                                ->where('date_end', '>=', Carbon::now());
                    });
            })
            ->orderBy('sort', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $query = Gallery::with(['item', 'member', 'images' => fn($q) => $q->orderBy('sort')]);
        // 基本条件：公開設定が有効なもののみ
        $query->where('active', 1);
        if (Auth::guard('member')->check()) {
            // ログインユーザーの場合
            // flag = 0（全員）または flag = 1（ログインユーザーのみ）を表示
            $query->whereIn('flag', [0, 1]);
        } else {
            // 非ログインユーザーの場合
            // flag = 0（全員）のみ表示
            $query->where('flag', 0);
        }

        $galleries = $query->where('active', 1)
                    ->orderByDesc('id')
                    ->limit(3)
                    ->get();

        // リアルタイムコメント取得（14日以内）
        $realtimeComments = Item::with(['itemImage' => function($query) {
                $query->thumbnailOrder();
            }])
            ->where('item_active', 1)
            ->where('item_p_comment', '!=', null)
            ->where('item_p_comment', '!=', '')
            ->where('item_p_comment_date', '>=', Carbon::now()->subDays(14))
            ->orderByDesc('item_p_comment_date')
            ->limit(10)
            ->get();

        // ファンクラブ最新投稿（公開済み、新しい順8件）
        $fanclubPosts = FanclubContent::with(['cast.itemImage' => function($q) {
                $q->thumbnailOrder();
            }, 'cast.fanclub'])
            ->visible()
            ->orderByDesc('published_at')
            ->limit(8)
            ->get();

        // メインビジュアル取得
        $mainVisualsPC = MainVisual::getActiveForPc();
        $mainVisualsMobile = MainVisual::getActiveForMobile();

        // 特設イベント取得
        $specialEvents = SpecialEvent::where('is_active', true)
            ->upcoming()
            ->orderBy('sort_order')
            ->orderBy('event_date')
            ->orderBy('start_hour')
            ->orderBy('start_minute')
            ->get();

        // カテゴリ取得
        $item_categories = ItemCategory::orderBy('sort')->get();
        $optionCates = OptionCate::orderBy('sort')->get();
        $options = Option::orderBy('sort')->get();
        $optionPays = OptionPay::orderBy('sort')->get();
        $links = Link::where('active', 1)
            ->orderBy('position', 'asc')
            ->orderBy('sort', 'asc')
            ->get();

        // ピックアップ
        $pickups = Pickup::with(['item.thumbnail', 'item.icon_items'])
            ->orderBy('sort', 'asc')
            ->get();

        // ランキング（rank_cates.sort → ranks.sort の順で全件取得、表示順はビュー側で制御）
        $rankCates = RankCate::orderBy('sort', 'asc')->get();
        $topRanks = Rank::with([
            'itemRanks' => fn($q) => $q->orderBy('no', 'asc'),
            'itemRanks.items.thumbnail',
        ])
        ->where('active', 1)
        ->orderBy('sort', 'asc')
        ->get();

        // 新人情報（newfaces テーブルから sort 順）
        $newfaces = Newface::with(['item.thumbnail', 'item.icon_items'])
            ->whereHas('item', fn($q) => $q->where('item_active', 1))
            ->orderBy('sort', 'asc')
            ->get();
        $newItems = $newfaces->map(fn($nf) => $nf->item)->filter()->values();

        // ショップ情報取得
        $shops = Shop::first();

        // 当日・翌日のスケジュール取得
        $adjustedDate = Carbon::parse(adjustTimeWithShift());
        $tomorrowDate = Carbon::parse(adjustTimeWithShift())->addDay();

        $scheduleFilter = function ($q) use ($request) {
            $q->when($request->filled('name'), function ($q2) use ($request) {
                $q2->whereHas('item', fn($q3) => $q3->where('item_name', 'LIKE', '%' . $request->name . '%'));
            })
            ->when($request->filled('item_category_ids'), function ($q2) use ($request) {
                $ids = array_map('intval', (array) $request->item_category_ids);
                $q2->whereHas('item', fn($q3) => $q3->whereIn('item_category_id', $ids));
            })
            ->when($request->filled('option_cates'), function ($q2) use ($request) {
                $cates = array_map('intval', (array) $request->option_cates);
                foreach ($cates as $cate) {
                    $q2->whereHas('item', function ($q3) use ($cate) {
                        $q3->where(function ($q4) use ($cate) {
                            $q4->whereHas('options', fn($q5) => $q5->where('cate', $cate))
                               ->orWhereHas('option_pays', fn($q5) => $q5->where('cate', $cate));
                        });
                    });
                }
            })
            ->when($request->filled('options'), function ($q2) use ($request) {
                foreach (array_map('intval', (array) $request->options) as $optionId) {
                    $q2->whereHas('item', fn($q3) => $q3->whereHas('options', fn($q4) => $q4->where('options.id', $optionId)));
                }
            })
            ->when($request->filled('option_pays'), function ($q2) use ($request) {
                foreach (array_map('intval', (array) $request->option_pays) as $payId) {
                    $q2->whereHas('item', fn($q3) => $q3->whereHas('option_pays', fn($q4) => $q4->where('option_pays.id', $payId)));
                }
            });
        };

        $schedules = Schedule::whereDate('date', $adjustedDate)
                            ->whereNotIn('flag', [1, 4])
                            ->with(['item', 'itemImage', 'itemImages'])
                            ->orderBy('sort', 'asc')
                            ->orderBy('time_start', 'asc');
        $scheduleFilter($schedules);
        $schedules = $schedules->get();

        $tomorrowSchedules = Schedule::whereDate('date', $tomorrowDate)
                            ->whereNotIn('flag', [1, 4])
                            ->with(['item', 'itemImage', 'itemImages'])
                            ->orderBy('sort', 'asc')
                            ->orderBy('time_start', 'asc');
        $scheduleFilter($tomorrowSchedules);
        $tomorrowSchedules = $tomorrowSchedules->get();

        // 予約データを取得（ローカルデータベース）
        try {
            // 各Scheduleに該当日付の予約をセット
            $schedules->each(function ($schedule) use ($adjustedDate) {
                try {
                    $reservations = Reservation::where('item_id', $schedule->item_id)
                        ->whereDate('date', $adjustedDate)
                        ->whereIn('flag', [1,2])
                        ->get();
                    $schedule->setRelation('reservations', $reservations);
                } catch (\Exception $e) {
                    \Log::warning('Failed to fetch reservations for schedule', [
                        'schedule_id' => $schedule->id,
                        'error' => $e->getMessage()
                    ]);
                    $schedule->setRelation('reservations', collect());
                }
            });

            // 翌日の予約をセット
            $tomorrowSchedules->each(function ($schedule) use ($tomorrowDate) {
                try {
                    $reservations = Reservation::where('item_id', $schedule->item_id)
                        ->whereDate('date', $tomorrowDate)
                        ->whereIn('flag', [1,2])
                        ->get();
                    $schedule->setRelation('reservations', $reservations);
                } catch (\Exception $e) {
                    $schedule->setRelation('reservations', collect());
                }
            });

            // スタジオ（設備）の予約を取得
            $equipments = Equipment::all();

            // 各Equipmentに該当日付の予約をセット
            $equipments->each(function ($equipment) use ($adjustedDate) {
                try {
                    // equipment_idを使用して検索
                    $equipmentId = $equipment->id;

                    // 該当日のスタジオ予約を取得
                    $reservations = Reservation::where('equipment_id', $equipmentId)
                        ->whereDate('date', $adjustedDate)
                        ->where('flag', '!=', 1) // 削除済み以外
                        ->get();

                    $equipment->setRelation('reservations', $reservations);
                } catch (\Exception $e) {
                    \Log::warning('Failed to fetch equipment reservations', [
                        'equipment_id' => $equipment->id,
                        'error' => $e->getMessage()
                    ]);
                    // エラー時は空のコレクションをセット
                    $equipment->setRelation('reservations', collect());
                }
            });
        } catch (\Exception $e) {
            \Log::error('Failed to initialize reservation model', [
                'error' => $e->getMessage()
            ]);
            // エラー時は空のEquipmentコレクションを作成
            $equipments = collect();
        }

        return view('/index', compact('items', 'news', 'newfaces', 'events', 'galleries', 'realtimeComments', 'fanclubPosts', 'mainVisualsPC', 'mainVisualsMobile', 'shops', 'schedules', 'adjustedDate', 'tomorrowSchedules', 'tomorrowDate', 'equipments', 'item_categories', 'optionCates', 'options', 'optionPays', 'links', 'specialEvents', 'topRanks', 'rankCates', 'newItems', 'pickups'));
    }
}
