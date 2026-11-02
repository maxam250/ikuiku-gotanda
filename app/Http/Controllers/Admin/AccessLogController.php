<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccessLogController extends Controller
{
    /** 表示・集計できる最大期間（日） */
    private const MAX_RANGE_DAYS = 92;

    public function index(Request $request)
    {
        // 期間の決定（デフォルト直近30日）
        $to = $request->filled('to')
            ? Carbon::parse($request->input('to'))->endOfDay()
            : Carbon::today()->endOfDay();

        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'))->startOfDay()
            : Carbon::today()->subDays(29)->startOfDay();

        // from > to の入れ違いを補正
        if ($from->gt($to)) {
            [$from, $to] = [$to->copy()->startOfDay(), $from->copy()->endOfDay()];
        }

        // 最大期間を超えないよう制限
        if ($from->lt($to->copy()->subDays(self::MAX_RANGE_DAYS))) {
            $from = $to->copy()->subDays(self::MAX_RANGE_DAYS)->startOfDay();
        }

        $base = DB::table('access_logs')->whereBetween('created_at', [$from, $to]);

        // 日別 PV / UU
        $daily = (clone $base)
            ->selectRaw('DATE(created_at) as d, COUNT(*) as pv, COUNT(DISTINCT visitor_id) as uu')
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->keyBy('d');

        // グラフ用に欠損日を0埋めした系列を作る
        $labels = [];
        $pvSeries = [];
        $uuSeries = [];
        $cursor = $from->copy();
        while ($cursor->lte($to)) {
            $key = $cursor->toDateString();
            $labels[]   = $cursor->format('n/j');
            $pvSeries[] = (int) ($daily[$key]->pv ?? 0);
            $uuSeries[] = (int) ($daily[$key]->uu ?? 0);
            $cursor->addDay();
        }

        // 合計
        $totalPv = (clone $base)->count();
        $totalUu = (clone $base)->distinct()->count('visitor_id');

        // リファラ集計（ホスト別 上位50）
        $referers = (clone $base)
            ->whereNotNull('referer_host')
            ->selectRaw('referer_host, COUNT(*) as pv, COUNT(DISTINCT visitor_id) as uu')
            ->groupBy('referer_host')
            ->orderByDesc('pv')
            ->limit(50)
            ->get();

        // リファラ無し（直接流入）
        $directPv = (clone $base)->whereNull('referer_host')->count();

        // 生ログ（最新100件）
        $rawLogs = (clone $base)
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        // 保存中の総行数（テーブル負荷の目安）
        $totalRows = DB::table('access_logs')->count();

        return view('admin.access_logs.index', [
            'from'          => $from,
            'to'            => $to,
            'labels'        => $labels,
            'pvSeries'      => $pvSeries,
            'uuSeries'      => $uuSeries,
            'daily'         => $daily,
            'totalPv'       => $totalPv,
            'totalUu'       => $totalUu,
            'referers'      => $referers,
            'directPv'      => $directPv,
            'rawLogs'       => $rawLogs,
            'totalRows'     => $totalRows,
            'retentionDays' => AccessLog::RETENTION_DAYS,
        ]);
    }
}
