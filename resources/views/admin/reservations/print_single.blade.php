<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予約詳細 印刷 - ID:{{ $reservation->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Hiragino Kaku Gothic ProN', 'Meiryo', sans-serif;
            font-size: 11px;
            color: #111;
            background: #fff;
            padding: 20px;
        }

        .no-print-btn {
            position: fixed;
            top: 14px;
            right: 18px;
            display: flex;
            gap: 8px;
            z-index: 999;
        }
        .btn-print {
            background: #EEEDED;
            color: #000;
            border: none;
            padding: 8px 20px;
            font-size: 13px;
            cursor: pointer;
            border-radius: 4px;
        }
        .btn-back {
            background: #666;
            color: #fff;
            border: none;
            padding: 8px 18px;
            font-size: 13px;
            cursor: pointer;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
        }

        .doc-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 14px;
            padding-bottom: 8px;
            border-bottom: 2px solid #252525;
        }
        .doc-header h1 {
            font-size: 20px;
            font-weight: bold;
            color: #252525;
        }
        .doc-header .meta {
            font-size: 9px;
            color: #666;
            text-align: right;
            line-height: 1.7;
        }

        .section {
            margin-bottom: 14px;
        }
        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #252525;
            background: #c9c9c9;
            padding: 3px 8px;
            margin-bottom: 0;
        }

        table.info-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.info-table th {
            width: 28%;
            background: #f4f4f4;
            padding: 5px 8px;
            font-size: 10px;
            font-weight: bold;
            color: #252525;
            border: 1px solid #cccccc;
            text-align: left;
            vertical-align: top;
        }
        table.info-table td {
            padding: 5px 8px;
            font-size: 11px;
            border: 1px solid #cccccc;
            vertical-align: top;
        }
        .td-large {
            font-size: 15px !important;
            font-weight: bold;
        }

        .fee-box {
            border: 1px solid #cccccc;
            border-top: none;
        }
        .fee-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 12px;
            border-bottom: 1px solid #ebebeb;
            font-size: 10px;
        }
        .fee-row:last-child { border-bottom: none; }
        .fee-row.total-intax {
            background: #f4f4f4;
            font-weight: bold;
            font-size: 16px;
            color: #252525;
            padding: 8px 12px;
        }
        .fee-row.total-extax {
            font-size: 10px;
            color: #555;
            padding: 4px 12px;
        }
        .fee-row .amount { font-weight: bold; }

        .two-col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm 10mm;
            }
            .no-print-btn { display: none !important; }
            body { padding: 0; }
            * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<div class="no-print-btn">
    <button class="btn-back" onclick="window.close()">✕ 閉じる</button>
    <button class="btn-print" onclick="window.print()">印刷する</button>
</div>

<div class="doc-header">
    <h1>{{ $shop->shop_name ?? '' }}　予約詳細</h1>
    <div class="meta">
        出力日時：{{ now()->format('Y年m月d日　H:i') }}
    </div>
</div>

<div class="two-col">

    {{-- 左カラム --}}
    <div>
        <div class="section">
            <div class="section-title">予約者情報</div>
            <table class="info-table">
                <tr>
                    <th>予約名</th>
                    <td class="td-large">{{ $reservation->name }}
                        @if($reservation->member_id)
                        <span style="font-size:9px;color:#888;font-weight:normal;margin-left:6px;">会員ID: {{ $reservation->member_id }}</span>
                        @else
                        <span style="font-size:9px;color:#888;font-weight:normal;margin-left:6px;">非会員</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">予約内容</div>
            <table class="info-table">
                <tr>
                    <th>予約形態</th>
                    <td>{{ $reservation->kind == 1 ? '通常予約' : 'リクエスト予約' }}</td>
                </tr>
                <tr>
                    <th>日付</th>
                    <td>{{ $reservation->date->format('Y年m月d日') }}（{{ $reservation->date->isoFormat('ddd') }}）</td>
                </tr>
                <tr>
                    <th>時間</th>
                    <td>
                        {{ $reservation->time_start ? $reservation->time_start->format('H:i') : '－' }}
                        〜
                        {{ $reservation->time_end ? $reservation->time_end->format('H:i') : '－' }}
                    </td>
                </tr>
                <tr>
                    <th>コース</th>
                    <td>
                        @foreach($courses as $course)
                            @if($course->id == $reservation->course_id){{ $course->title }}@endif
                        @endforeach
                        @if($reservation->course_time)
                            &nbsp;/&nbsp;{{ $reservation->course_time }}分
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>キャスト</th>
                    <td class="td-large">{{ $reservation->item_name }}
                        @if($reservation->item_name_other)
                        <br><span style="font-size:9px;color:#666;font-weight:normal;">{{ $reservation->item_name_other }}</span>
                        @endif
                    </td>
                </tr>
                @if($reservation->course_id != 3)
                <tr>
                    <th>場所 / スタジオ</th>
                    <td>
                        @foreach($equipments as $equipment)
                            @php $eId = $equipment->external_id ?? $equipment->id; @endphp
                            @if($eId == $reservation->equipment_id){{ $equipment->title }}@endif
                        @endforeach
                    </td>
                </tr>
                @else
                <tr>
                    <th>エリア</th>
                    <td>{{ $reservation->area }}</td>
                </tr>
                <tr>
                    <th>場所</th>
                    <td>{{ $reservation->place }}</td>
                </tr>
                @if($reservation->address)
                <tr>
                    <th>住所</th>
                    <td>{{ $reservation->address }}</td>
                </tr>
                @endif
                @if($reservation->meet)
                <tr>
                    <th>待ち合わせ</th>
                    <td>{{ $reservation->meet }}</td>
                </tr>
                @endif
                @endif
            </table>
        </div>
    </div>

    {{-- 右カラム --}}
    <div>
        @if($reservation->option && count((array)$reservation->option) > 0)
        <div class="section">
            <div class="section-title">オプション</div>
            <table class="info-table">
                <tr>
                    <th>無料オプション</th>
                    <td>
                        @foreach($options as $option)
                            @if(is_array($reservation->option) && in_array($option->id, $reservation->option))
                                {{ $option->title }}&nbsp;
                            @endif
                        @endforeach
                    </td>
                </tr>
            </table>
        </div>
        @endif

        @if($reservation->option_pay && count((array)$reservation->option_pay) > 0)
        <div class="section">
            <div class="section-title">有料オプション</div>
            <table class="info-table">
                <tr>
                    <th>有料オプション</th>
                    <td>
                        @foreach($option_pays as $op)
                            @if(is_array($reservation->option_pay) && in_array($op->id, $reservation->option_pay))
                                {{ $op->title }}&nbsp;
                            @endif
                        @endforeach
                    </td>
                </tr>
            </table>
        </div>
        @endif

        @if($reservation->comment)
        <div class="section">
            <div class="section-title">備考（お客様）</div>
            <table class="info-table">
                <tr><td style="white-space:pre-wrap;">{{ $reservation->comment }}</td></tr>
            </table>
        </div>
        @endif

        @if($reservation->memo)
        <div class="section">
            <div class="section-title">メモ（管理者のみ）</div>
            <table class="info-table">
                <tr><td style="white-space:pre-wrap;">{{ $reservation->memo }}</td></tr>
            </table>
        </div>
        @endif

        @if($reservation->memo_share)
        <div class="section">
            <div class="section-title">メモ（キャスト共有）</div>
            <table class="info-table">
                <tr><td style="white-space:pre-wrap;">{{ $reservation->memo_share }}</td></tr>
            </table>
        </div>
        @endif

        <div class="section">
            <div class="section-title">料金</div>
            <div class="fee-box">
                <div class="fee-row">
                    <span>コース料金</span>
                    <span class="amount">{{ number_format($reservation->fee_course) }} 円</span>
                </div>
                @if($reservation->fee_option)
                <div class="fee-row">
                    <span>オプション料金</span>
                    <span class="amount">{{ number_format($reservation->fee_option) }} 円</span>
                </div>
                @endif
                @if($reservation->fee_adjust)
                <div class="fee-row">
                    <span>調整金{{ $reservation->fee_adjust_memo ? '（'.$reservation->fee_adjust_memo.'）' : '' }}</span>
                    <span class="amount">{{ number_format($reservation->fee_adjust) }} 円</span>
                </div>
                @endif
                <div class="fee-row total-intax">
                    <span>税込金額</span>
                    <span>{{ number_format($reservation->fee_total * config('tax.intax')) }} 円</span>
                </div>
                <div class="fee-row total-extax">
                    <span>税抜金額</span>
                    <span>{{ number_format($reservation->fee_total) }} 円</span>
                </div>
            </div>
        </div>
    </div>

</div>

</body>
</html>
