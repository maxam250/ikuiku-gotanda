<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>予約確認</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2563eb; border-bottom: 2px solid #2563eb; padding-bottom: 10px;">
            @if($isShop)
                予約の申し込みがありました
            @else
                ご予約ありがとうございます
            @endif
        </h2>

        @if(!$isShop)
        <p>{{ $reservation->name }} 様</p>
        <p>いつも「{{ $shops->shop_name }}」をご利用いただきまして誠にありがとうございます。<br>
        ご予約のお申し込みを送信しました。<br>
        ショップより折り返しでご連絡しますので少々お待ちくださいませ。</p>
        @endif

        <h3 style="color: #2563eb; margin-top: 30px;">予約内容</h3>

        @if($isShop)
        <div style="padding:1rem;background:#f0f0f0;margin-bottom:1rem;border-radius:5px;">
            <h4 style="margin-top:0;color:#2563eb;">申込者情報</h4>
            <p><strong>お名前:</strong> {{ $reservation->name }} 様</p>
            @if(!Auth::guard('member')->check())
            <p><strong>種別:</strong> ビジター</p>
            <p><strong>Eメール:</strong> {{ $reservation->email }}</p>
            <p><strong>電話番号:</strong> {{ $reservation->tel }}</p>
            @else
            <p><strong>種別:</strong> 会員</p>
            <p><strong>会員詳細:</strong> {{ config('app.url') }}/admin/members/{{ $reservation->member_id }}/edit</p>
            @endif
        </div>
        @endif

        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold; width: 30%; background-color: #f8f9fa;">予約日時</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $reservation->date->format('Y年m月d日') . '（' . $reservation->date->isoFormat('ddd') . '）' }} {{ $reservation->time_start->format('H:i') }}</td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold; background-color: #f8f9fa;">コース</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">
                    @foreach($courses as $course)
                        @if($course->id == $reservation['course_id'])
                            {{ $course->title }}
                            @if($reservation->course_time)
                                / {{ $reservation->course_time }}分
                            @endif
                        @endif
                    @endforeach
                </td>
            </tr>
            <tr>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold; background-color: #f8f9fa;">キャスト</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $reservation->item_name }}</td>
            </tr>
            @php
                $option_ar = json_decode($reservation->option, true);
                $option_pay_ar = json_decode($reservation->option_pay, true);
                $costume_ar = json_decode($reservation->costume, true);
            @endphp
            @if(!empty($option_ar))
            <tr>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold; background-color: #f8f9fa;">オプション</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">
                    @foreach($option_ar ?? [] as $val)
                        @foreach($options as $option)
                            @if ($option->id == $val)
                                {{$option->title}}@if(!$loop->last), @endif
                            @endif
                        @endforeach
                    @endforeach
                </td>
            </tr>
            @endif
            @if(!empty($option_pay_ar))
            <tr>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold; background-color: #f8f9fa;">有料オプション</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">
                    @foreach($option_pay_ar ?? [] as $val)
                        @foreach($option_pays as $option_pay)
                            @if ($option_pay->id == $val)
                                {{$option_pay->title}}@if(!$loop->last), @endif
                            @endif
                        @endforeach
                    @endforeach
                </td>
            </tr>
            @endif
            <tr>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold; background-color: #f8f9fa;">スタジオ</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">
                    @foreach($equipments as $equipment)
                        @if ($equipment->id == $reservation->equipment_id)
                            {{$equipment->title}}
                        @endif
                    @endforeach
                </td>
            </tr>
            @if(!empty($costume_ar))
            <tr>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold; background-color: #f8f9fa;">コスチューム</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">
                    @foreach($costume_ar ?? [] as $val)
                        @foreach($costumes as $costume)
                            @if ($costume->id == $val)
                                {{$costume->name}}@if(!$loop->last), @endif
                            @endif
                        @endforeach
                    @endforeach
                </td>
            </tr>
            @endif

            @php
                $myoption_ar = json_decode($reservation->myoption, true);
                $mycostume_ar = json_decode($reservation->mycostume, true);
            @endphp

            @if(!empty($myoption_ar))
            <tr>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold; background-color: #f8f9fa;">マイオプション</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">
                    @foreach($items as $item)
                        @if($item->id == $reservation->item_id)
                            @foreach($item->myOptions as $myOption)
                                @if(in_array($myOption->id, $myoption_ar ?? []))
                                    {{$myOption->name}}@if(!$loop->last), @endif
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </td>
            </tr>
            @endif

            @if(!empty($mycostume_ar))
            <tr>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold; background-color: #f8f9fa;">マイコスチューム</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">
                    @foreach($items as $item)
                        @if($item->id == $reservation->item_id)
                            @foreach($item->myCostumes as $myCostume)
                                @if(in_array($myCostume->id, $mycostume_ar ?? []))
                                    {{$myCostume->name}}@if(!$loop->last), @endif
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </td>
            </tr>
            @endif

            @if($reservation->area)
            <tr>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold; background-color: #f8f9fa;">ご利用エリア</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $reservation->area }}</td>
            </tr>
            @endif
            @if($reservation->place)
            <tr>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold; background-color: #f8f9fa;">具体的な場所</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $reservation->place }}</td>
            </tr>
            @endif
            @if($reservation->meet)
            <tr>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold; background-color: #f8f9fa;">待ち合わせ方法</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $reservation->meet }}</td>
            </tr>
            @endif
            @if($reservation->address)
            <tr>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold; background-color: #f8f9fa;">住所</td>
                <td style="padding: 10px; border: 1px solid #dee2e6;">{{ $reservation->address }}</td>
            </tr>
            @endif
            @if($reservation->comment)
            <tr>
                <td style="padding: 10px; border: 1px solid #dee2e6; font-weight: bold; background-color: #f8f9fa;">備考</td>
                <td style="padding: 10px; border: 1px solid #dee2e6; white-space: pre-wrap;">{!! nl2br(e($reservation->comment)) !!}</td>
            </tr>
            @endif
        </table>

        @if($reservation->item_id)
        <div style="padding:1rem;background:#fffddb;margin:20px 0;border-radius:5px;">
            <h4 style="margin-top:0;color:#2563eb;">料金</h4>
            <p><strong>コース料金:</strong> {{ number_format($reservation->fee_course) }}円</p>
            @if($reservation->fee_option > 0)
            <p><strong>オプション料金:</strong> {{ number_format($reservation->fee_option) }}円</p>
            @endif
            <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ccc;">
                <p><strong>小　計:</strong> {{ number_format($reservation->fee_course + $reservation->fee_option + $reservation->fee_myoption + $reservation->fee_mycostume) }}円</p>
                <p><strong>消費税:</strong> {{ number_format(floor(($reservation->fee_course + $reservation->fee_option + $reservation->fee_myoption + $reservation->fee_mycostume) * 0.1)) }}円</p>
                <p><strong>合　計:</strong> {{ number_format($reservation->fee_total) }}円</p>
            </div>
            @if($reservation->course_id == 3)
            <p style="color: #856404; background-color: #fff3cd; padding: 10px; border-radius: 3px; margin-top: 10px;">
                ※移動費、交通費は集計してショップからのメールにてお知らせいたします。
            </p>
            @endif
        </div>
        @else
        <div style="padding:1rem;background:#d1ecf1;margin:20px 0;border-radius:5px;">
            <p style="margin:0;color:#0c5460;"><strong>料金:</strong> 料金はキャストにより異なるため追ってご連絡いたします</p>
        </div>
        @endif

        <hr style="margin: 30px 0; border: none; border-top: 1px solid #dee2e6;">

        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px;">
            <h4 style="margin-top:0;color:#2563eb;">{{ $shops->shop_name }}</h4>
            <p style="margin: 5px 0;"><strong>営業時間:</strong> {{ $shops->shop_open }}</p>
            <p style="margin: 5px 0;"><strong>電話番号:</strong> {{ $shops->shop_tel }}</p>
            <p style="margin: 5px 0;"><strong>ホームページ:</strong> {{ $shops->shop_url }}</p>
        </div>

        <p style="font-size: 12px; color: #666; margin-top: 20px;">
            このメールは自動送信されています。<br>
            送信日時: {{ now()->format('Y年m月d日 H:i') }}
        </p>

        <p style="font-size: 12px; color: #666;">
            ※このメールに返信いただいても回答できませんのでご了承ください。<br>
            お問い合わせはホームページまたは電話からお願いいたします。
        </p>
    </div>
</body>
</html>
