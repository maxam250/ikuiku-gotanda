<?php

namespace App\Http\Controllers;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemImage;
use App\Models\Myoption;
use App\Models\MyoptionCate;
use App\Models\Mycostume;
use App\Models\MycostumeCate;
use App\Models\Option;
use App\Models\OptionCate;
use App\Models\OptionPay;
use App\Models\Fanclub;
use App\Models\FanclubMembership;
use App\Models\Interview;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    //
    public function index(Request $request){
        $query = Item::select('items.*')
            ->addSelect([
                'filename' => ItemImage::select('filename')
                    ->whereColumn('item_id', 'items.id')
                    ->thumbnailOrder()
                    ->limit(1)
            ])
            ->where('items.item_active', 1);

        // 検索条件の適用
        if ($request->filled('name')) {
            $query->where('item_name', 'LIKE', '%' . $request->name . '%');
        }

        // サイズ検索
        if ($request->filled('height')) {
            $heightRanges = $request->height;
            $query->where(function($q) use ($heightRanges) {
                foreach ($heightRanges as $range) {
                    $parts = explode('-', $range);
                    if (count($parts) === 2) {
                        $q->orWhereBetween('item_height', [(int)$parts[0], (int)$parts[1]]);
                    }
                }
            });
        }

        if ($request->filled('bust')) {
            $bustRanges = $request->bust;
            $query->where(function($q) use ($bustRanges) {
                foreach ($bustRanges as $range) {
                    $parts = explode('-', $range);
                    if (count($parts) === 2) {
                        $q->orWhereBetween('item_b', [(int)$parts[0], (int)$parts[1]]);
                    }
                }
            });
        }

        if ($request->filled('waist')) {
            $waistRanges = $request->waist;
            $query->where(function($q) use ($waistRanges) {
                foreach ($waistRanges as $range) {
                    $parts = explode('-', $range);
                    if (count($parts) === 2) {
                        $q->orWhereBetween('item_w', [(int)$parts[0], (int)$parts[1]]);
                    }
                }
            });
        }

        if ($request->filled('hip')) {
            $hipRanges = $request->hip;
            $query->where(function($q) use ($hipRanges) {
                foreach ($hipRanges as $range) {
                    $parts = explode('-', $range);
                    if (count($parts) === 2) {
                        $q->orWhereBetween('item_h', [(int)$parts[0], (int)$parts[1]]);
                    }
                }
            });
        }

        // カテゴリ検索（単一・旧）
        if ($request->filled('category_id')) {
            $query->where('item_category_id', $request->category_id);
        }

        // キャストカテゴリ検索（複数選択）
        if ($request->filled('item_category_ids')) {
            $ids = array_map('intval', (array) $request->item_category_ids);
            $query->whereIn('item_category_id', $ids);
        }

        // 個別オプション検索（複数選択は全て一致）
        if ($request->filled('options')) {
            foreach (array_map('intval', (array) $request->options) as $optionId) {
                $query->whereHas('options', function ($q) use ($optionId) {
                    $q->where('options.id', $optionId);
                });
            }
        }

        if ($request->filled('option_pays')) {
            foreach (array_map('intval', (array) $request->option_pays) as $payId) {
                $query->whereHas('option_pays', function ($q) use ($payId) {
                    $q->where('option_pays.id', $payId);
                });
            }
        }

        // オプションカテゴリ検索（options または option_pays のいずれかが該当カテゴリを持つ）
        if ($request->filled('option_cates')) {
            $cates = array_map('intval', (array) $request->option_cates);
            foreach ($cates as $cate) {
                $query->where(function($q) use ($cate) {
                    $q->whereHas('options', function($q2) use ($cate) {
                        $q2->where('cate', $cate);
                    })->orWhereHas('option_pays', function($q2) use ($cate) {
                        $q2->where('cate', $cate);
                    });
                });
            }
        }

        $items = $query->with([
            'category',
            'fanclub'    => fn($q) => $q->where('status', 'active'),
            'myoptions'  => fn($q) => $q->where('active', 1),
            'mycostumes' => fn($q) => $q->where('active', 1),
        ])->orderByRaw('COALESCE(items.item_sort, 999999) ASC')
            ->orderBy('items.id', 'desc')
            ->paginate(100);

        // 検索用のオプションデータを取得
        $options = Option::orderBy('sort')->get();
        $optionPays = OptionPay::orderBy('sort')->get();
        $item_categories = ItemCategory::orderBy('sort')->get();
        $optionCates = OptionCate::orderBy('sort')->get();

        return view('item', compact('items', 'options', 'optionPays', 'item_categories', 'optionCates'));
    }

    public function twitter()
    {
        $items = Item::select('items.*')
            ->addSelect([
                'filename' => ItemImage::select('filename')
                    ->whereColumn('item_id', 'items.id')
                    ->thumbnailOrder()
                    ->limit(1)
            ])
            ->where('items.item_active', 1)
            ->whereNotNull('items.item_twitter')
            ->where('items.item_twitter', '!=', '')
            ->with([
                'category',
                'fanclub'    => fn($q) => $q->where('status', 'active'),
                'myoptions'  => fn($q) => $q->where('active', 1),
                'mycostumes' => fn($q) => $q->where('active', 1),
            ])
            ->orderByRaw('COALESCE(items.item_sort, 999999) ASC')
            ->orderBy('items.id', 'desc')
            ->paginate(100);

        return view('x', compact('items'));
    }

    public function movie()
    {
        $items = Item::select('items.*')
            ->addSelect([
                'filename' => ItemImage::select('filename')
                    ->whereColumn('item_id', 'items.id')
                    ->thumbnailOrder()
                    ->limit(1)
            ])
            ->where('items.item_active', 1)
            ->whereNotNull('items.item_video')
            ->where('items.item_video', '!=', '')
            ->with([
                'category',
                'fanclub'    => fn($q) => $q->where('status', 'active'),
                'myoptions'  => fn($q) => $q->where('active', 1),
                'mycostumes' => fn($q) => $q->where('active', 1),
            ])
            ->orderByRaw('COALESCE(items.item_sort, 999999) ASC')
            ->orderBy('items.id', 'desc')
            ->paginate(100);

        return view('movie', compact('items'));
    }

    public function newface()
    {
        $items = Item::select('items.*')
            ->addSelect([
                'filename' => ItemImage::select('filename')
                    ->whereColumn('item_id', 'items.id')
                    ->thumbnailOrder()
                    ->limit(1)
            ])
            ->join('newfaces', 'newfaces.item_id', '=', 'items.id')
            ->where('items.item_active', 1)
            ->with([
                'category',
                'fanclub'    => fn($q) => $q->where('status', 'active'),
                'myoptions'  => fn($q) => $q->where('active', 1),
                'mycostumes' => fn($q) => $q->where('active', 1),
            ])
            ->orderBy('newfaces.sort', 'asc')
            ->orderBy('items.id', 'desc')
            ->paginate(100);

        return view('newface', compact('items'));
    }


    public function show($id){

        $item = Item::with(['schedules', 'interviews', 'courses', 'courseFees', 'category', 'options', 'option_pays', 'icon_items', 'profiles'])->findOrFail($id);  // 指定されたIDのキャストを取得

        // 画像リストを取得
        $images = ItemImage::where('item_id', $id)
                            ->orderby('sort', 'asc')->get();
        // 該当するキャストのマイオプションのみを取得
        $myoptions = Myoption::where('item_id', $id)
                            ->where('active', 1)
                            ->orderby('sort', 'asc')->get();
        // 該当するキャストのマイコスチュームのみを取得
        $mycostumes = Mycostume::where('item_id', $id)
                            ->where('active', 1)
                            ->orderby('sort', 'asc')->get();

        $mycostumeCates = MycostumeCate::orderBy('sort', 'asc')->get();
        $myoptionCates = MyoptionCate::orderBy('sort', 'asc')->get();
        $optionCates = OptionCate::orderBy('sort', 'asc')->get();

        // dd( $mycostume);

        //出勤
        // 全期間のスケジュールを取得（プロフィールページ用）
        // with(['schedules']) で取得済みのコレクションをメモリ上でソート（追加クエリなし）
        $schedules = $item->schedules->sortBy('date')->values();

        // ※ このプロジェクトでは reservations を現在使用していないため、
        //    Reservation クエリ・デバッグログは無効化しています。
        //    別プロジェクトへ clone して再利用する場合は以下を復元してください。
        //
        // $reservations = \App\Models\Reservation::where('item_id', $id)
        //     ->where('date', '>=', adjustTimeWithShift())
        //     ->whereIn('flag', [1,2])
        //     ->orderBy('date', 'asc')
        //     ->orderBy('time_start', 'asc')
        //     ->get();


        // このキャストが参加している特設イベントを取得
        $specialEvents = \App\Models\SpecialEvent::with([])
            ->whereHas('items', function($query) use ($id) {
                $query->where('item_id', $id);
            })
            ->active()
            ->orderBy('sort_order', 'asc')
            ->orderBy('event_date', 'asc')
            ->orderBy('start_hour', 'asc')
            ->orderBy('start_minute', 'asc')
            ->get();

        // dd($item);


        // ファンクラブ情報
        $member  = Auth::guard('member')->user();
        $fanclub = Fanclub::where('cast_id', $id)
            ->where('status', 'active')
            ->withCount(['memberships as active_member_count' => fn($q) => $q->where('status', 'active')])
            ->first();

        $isFanMember = false;
        if ($fanclub && $member) {
            $isFanMember = FanclubMembership::where('fanclub_id', $fanclub->id)
                ->where('member_id', $member->id)
                ->where('status', 'active')
                ->exists();
        }

        $pricingType = Setting::firstOrCreate([])->pricing_type ?? 'unified';

        return view('prof', compact('item', 'images', 'schedules', 'myoptions', 'mycostumes','mycostumeCates', 'myoptionCates', 'optionCates', 'specialEvents', 'fanclub', 'member', 'isFanMember', 'pricingType'));

    }


}
