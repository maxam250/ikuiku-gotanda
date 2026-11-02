<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予約一覧 印刷</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Hiragino Kaku Gothic ProN', 'Meiryo', sans-serif;
            font-size: 10px;
            color: #111;
            background: #fff;
        }

        .print-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 2px solid #333;
        }

        .print-header h1 {
            font-size: 16px;
            font-weight: bold;
        }

        .print-header .meta {
            font-size: 9px;
            color: #555;
            text-align: right;
            line-height: 1.5;
        }

        .filter-info {
            font-size: 9px;
            color: #555;
            margin-bottom: 8px;
            padding: 4px 6px;
            background: #f5f5f5;
            border-left: 3px solid #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        thead tr {
            background: #333;
            color: #fff;
        }

        thead th {
            padding: 5px 4px;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid #555;
        }

        tbody tr {
            border-bottom: 1px solid #ccc;
        }

        tbody tr:nth-child(even) {
            background: #f9f9f9;
        }

        tbody td {
            padding: 4px 4px;
            vertical-align: top;
            font-size: 9px;
            border: 1px solid #ddd;
            word-break: break-all;
        }

        .col-id    { width: 4%; }
        .col-date  { width: 11%; }
        .col-name  { width: 11%; }
        .col-course{ width: 12%; }
        .col-cast  { width: 10%; }
        .col-loc   { width: 13%; }
        .col-memo  { width: 14%; }
        .col-share { width: 14%; }
        .col-fee   { width: 8%; text-align: right; }
        .col-flag  { width: 6%; text-align: center; }

        .text-center { text-align: center; }
        .text-right  { text-align: right; }
        .text-muted  { color: #777; font-size: 8px; }

        .flag-badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 8px;
            font-weight: bold;
        }
        .flag-0 { background: #eee; color: #555; }
        .flag-1 { background: #ffe0e0; color: #c00; }
        .flag-2 { background: #d4f0d4; color: #060; }
        .flag-3 { background: #e0e0ff; color: #006; }
        .flag-4 { background: #fff3b0; color: #660; }

        .no-print-btn {
            position: fixed;
            top: 12px;
            right: 16px;
            display: flex;
            gap: 8px;
            z-index: 999;
        }

        .btn-print {
            background: #333;
            color: #fff;
            border: none;
            padding: 8px 18px;
            font-size: 13px;
            cursor: pointer;
            border-radius: 4px;
        }

        .btn-back {
            background: #888;
            color: #fff;
            border: none;
            padding: 8px 18px;
            font-size: 13px;
            cursor: pointer;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
        }

        .summary {
            margin-top: 10px;
            font-size: 9px;
            text-align: right;
            color: #444;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 10mm 8mm;
            }

            .no-print-btn {
                display: none !important;
            }

            body {
                font-size: 9px;
            }

            thead tr {
                background: #333 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            tbody tr:nth-child(even) {
                background: #f9f9f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .flag-badge {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<div class="no-print-btn">
    <a href="{{ url()->previous() }}" class="btn-back">← 戻る</a>
    <button class="btn-print" onclick="window.print()">印刷する</button>
</div>

<div class="print-header">
    <h1>予約一覧</h1>
    <div class="meta">
        出力日時：{{ now()->format('Y年m月d日　H:i') }}<br>
        件数：{{ $reservations->count() }} 件
    </div>
</div>

@php
    $hasFilter = !empty($searchParams['date']) || ($searchParams['flag'] !== null && $searchParams['flag'] !== '') || !empty($searchParams['itemId']) || !empty($searchParams['memberId']);
@endphp

@if($hasFilter)
<div class="filter-info">
    絞り込み条件：
    @if(!empty($searchParams['date']))日付：{{ $searchParams['date'] }}　@endif
    @if($searchParams['flag'] !== null && $searchParams['flag'] !== '')
        ステータス：{{ ['0'=>'未設定','1'=>'確認中','2'=>'確定','3'=>'保留','4'=>'キャンセル'][$searchParams['flag']] ?? $searchParams['flag'] }}
    @endif
    @if(!empty($searchParams['itemId']))
        キャスト：{{ $items->firstWhere('id', $searchParams['itemId'])?->item_name ?? $searchParams['itemId'] }}
    @endif
</div>
@endif

<table>
    <thead>
        <tr>
            <th class="col-id">ID</th>
            <th class="col-date">日時</th>
            <th class="col-name">予約名</th>
            <th class="col-course">コース</th>
            <th class="col-cast">キャスト</th>
            <th class="col-loc">場所</th>
            <th class="col-memo">メモ</th>
            <th class="col-share">共有メモ</th>
            <th class="col-fee">料金</th>
            <th class="col-flag">状態</th>
        </tr>
    </thead>
    <tbody>
        @forelse($reservations as $reservation)
        <tr>
            <td class="col-id text-center">{{ $reservation->id }}</td>
            <td class="col-date">
                {{ $reservation->date->isoFormat('MM/DD(ddd)') }}<br>
                {{ $reservation->time_start ? $reservation->time_start->format('H:i') : '' }}
                @if($reservation->time_end)
                 -{{ $reservation->time_end->format('H:i') }}
                @endif
            </td>
            <td class="col-name">
                @if($reservation->member_id)
                <span class="text-muted">M{{ $reservation->member_id }}</span><br>
                @else
                <span class="text-muted">非会員</span><br>
                @endif
                {{ $reservation->name }}
            </td>
            <td class="col-course">
                @foreach($courses as $course)
                    @if($course->id == $reservation->course_id){{ $course->title }}@endif
                @endforeach
                @if($reservation->course_time)
                <br><span class="text-muted">{{ $reservation->course_time }}分</span>
                @endif
            </td>
            <td class="col-cast">
                {{ $reservation->item_name }}
                @if($reservation->item_name_other)
                <br><span class="text-muted">{{ $reservation->item_name_other }}</span>
                @endif
            </td>
            <td class="col-loc">
                @if($reservation->course_id != 3)
                    @foreach($equipments as $equipment)
                        @php $equipmentId = $equipment->external_id ?? $equipment->id; @endphp
                        @if($equipmentId == $reservation->equipment_id){{ $equipment->title }}@endif
                    @endforeach
                @else
                    @if($reservation->area){{ $reservation->area }}@endif
                    @if($reservation->place)<br>{{ $reservation->place }}@endif
                    @if($reservation->address)<br><span class="text-muted">{{ $reservation->address }}</span>@endif
                @endif
            </td>
            <td class="col-memo">{{ $reservation->memo }}</td>
            <td class="col-share">{{ $reservation->memo_share }}</td>
            <td class="col-fee text-right">
                {{ number_format($reservation->fee_total) }}円<br>
                <span class="text-muted">税込{{ number_format($reservation->fee_total * config('tax.intax')) }}</span>
            </td>
            <td class="col-flag text-center">
                @php
                    $flagLabels = ['0'=>'未設定','1'=>'確認中','2'=>'確定','3'=>'保留','4'=>'キャンセル'];
                    $flagVal = (string)$reservation->flag;
                @endphp
                <span class="flag-badge flag-{{ $flagVal }}">{{ $flagLabels[$flagVal] ?? '－' }}</span>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="10" class="text-center" style="padding:16px;color:#999;">データがありません</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="summary">合計 {{ $reservations->count() }} 件　／　合計金額（税抜）：{{ number_format($reservations->sum('fee_total')) }} 円</div>

</body>
</html>
