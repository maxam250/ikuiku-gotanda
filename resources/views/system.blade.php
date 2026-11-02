@extends('layouts.app')

@section('breadcrumb_label', '料金とご利用案内')
@section('content')
<div class="titleArea">
    <h2>SYSTEM</h2>
    <div class="title">料金とご利用案内</div>
</div>

<div class="sys-page">

    {{-- ══════════════════════════════════════════════
         料金表
    ══════════════════════════════════════════════ --}}
    <section class="sys-section" id="sys-price">
        <h3 class="sys-heading">
            <span class="sys-heading-en">PRICE</span>
            <span class="sys-heading-ja">料金表</span>
        </h3>

        {{-- 料金は旧サイト（_gotanda/www_tpl/system.tpl）からの静的コピー。料金改定時はここを直接編集する --}}
        <div class="sys-static-price">
            <table class="sys-static-table">
                <tr>
                    <th>入会金</th>
                    <td>無料</td>
                    <th>写真指名</th>
                    <td>2,000円</td>
                </tr>
                <tr>
                    <th>本指名別途</th>
                    <td>1,000円</td>
                    <td colspan="2"></td>
                </tr>
            </table>

            <table class="sys-static-table">
                <tr>
                    <th>45分</th>
                    <td>9,500円<span class="sys-static-tax">+別途消費税</span></td>
                    <th>60分</th>
                    <td>11,000円<span class="sys-static-tax">+別途消費税</span></td>
                </tr>
                <tr>
                    <th>75分</th>
                    <td>13,000円<span class="sys-static-tax">+別途消費税</span></td>
                    <th>90分</th>
                    <td>15,000円<span class="sys-static-tax">+別途消費税</span></td>
                </tr>
                <tr>
                    <th>120分</th>
                    <td>23,000円<span class="sys-static-tax">+別途消費税</span></td>
                    <th></th>
                    <td></td>
                </tr>
            </table>
        </div>

        {{-- ▼ 動的表示（固定料金・管理画面のコース料金）は停止中。戻す場合は @if(false) を外す --}}
        @if(false)
        {{-- 固定料金 --}}
        <div class="sys-fixed-fees">
            <div class="sys-fixed-fee">
                <span class="sys-fixed-fee-label">入会金</span>
                <span class="sys-fixed-fee-value">無料</span>
            </div>
            <div class="sys-fixed-fee">
                <span class="sys-fixed-fee-label">写真指名</span>
                <span class="sys-fixed-fee-value">2,000<small>円</small></span>
            </div>
            <div class="sys-fixed-fee">
                <span class="sys-fixed-fee-label">本指名</span>
                <span class="sys-fixed-fee-value">3,000<small>円</small></span>
            </div>
            <div class="sys-fixed-fee">
                <span class="sys-fixed-fee-label">&nbsp;</span>
                <span class="sys-fixed-fee-value">別途消費税</span>
            </div>
        </div>

        {{-- コース料金 --}}


        @if(request('mode') !== 'test' && now()->lt(\Carbon\Carbon::parse('2026-07-15 06:00:00')))
        <div class="sys-courses">
            <div class="sys-course-block">
                    <table class="sys-fee-table">
                        <tbody>
                        <tr>
                        <th>45分</th>
                        <td class="sys-fee-price">10,500<small>円</small></td>
                        <th>60分</th>
                        <td class="sys-fee-price">12,000<small>円</small></td>
                        </tr>
                        <tr>
                        <th>75分</th>
                        <td class="sys-fee-price">14,000<small>円</small></td>
                        <th>90分</th>
                        <td class="sys-fee-price">16,000<small>円</small></td>
                        </tr>
                        <tr>
                        <th>120分</th>
                        <td class="sys-fee-price">24,000<small>円</small></td>
                        <th></th><td></td>
                        </tr>
                        </tbody>
                    </table>
            </div>
        </div>
        @else
        @if($courses->isNotEmpty())
        <div class="sys-courses">
            @foreach($courses as $course)
            <div class="sys-course-block">
                {{-- <h4 class="sys-course-title">{{ $course->title }}</h4> --}}
                @if($course->body)
                <p class="sys-course-body">{{ $course->body }}</p>
                @endif
                @if($course->course_fee->isNotEmpty())
                @php $fees = $course->course_fee->values(); @endphp
                <table class="sys-fee-table">
                    <tbody>
                        @for($i = 0; $i < $fees->count(); $i += 2)
                        <tr>
                            <th>{{ $fees[$i]->course }}</th>
                            <td class="sys-fee-price">{{ number_format($fees[$i]->fee) }}<small>円</small><span class="text-xs text-gray-400 ml-2">+別途消費税</span></td>
                            @if(isset($fees[$i + 1]))
                            <th>{{ $fees[$i + 1]->course }}</th>
                            <td class="sys-fee-price">{{ number_format($fees[$i + 1]->fee) }}<small>円</small><span class="text-xs text-gray-400 ml-2">+別途消費税</span></td>
                            @else
                            <th></th><td></td>
                            @endif
                        </tr>
                        @endfor
                    </tbody>
                </table>
                @endif
            </div>
            @endforeach
        </div>
        @endif
        @endif
        @endif{{-- /@if(false) --}}
    </section>

    {{-- ══════════════════════════════════════════════
         基本サービス
    ══════════════════════════════════════════════ --}}
    <section class="sys-section" id="sys-service">
        <h3 class="sys-heading">
            <span class="sys-heading-en">SERVICE</span>
            <span class="sys-heading-ja">基本サービス</span>
        </h3>
        <div class="sys-service-grid">
            <div class="sys-service-item">
                <span>生フェラ</span>
            </div>
            <div class="sys-service-item">
                <span>Dキス</span>
            </div>
            <div class="sys-service-item">
                <span>全身リップ</span>
            </div>
            <div class="sys-service-item">
                <span>玉舐め</span>
            </div>
            <div class="sys-service-item">
                <span>69</span>
            </div>
            <div class="sys-service-item">
                <span>指入れ</span>
            </div>
            <div class="sys-service-item">
                <span>手コキ</span>
            </div>
            <div class="sys-service-item">
                <span>素股</span>
            </div>
            <div class="sys-service-item">
                <span>口内発射</span>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════
         無料オプション
    ══════════════════════════════════════════════ --}}
    <section class="sys-section" id="sys-option-free">
        <h3 class="sys-heading">
            <span class="sys-heading-en">OPTION</span>
            <span class="sys-heading-ja">オプション</span>
            {{--
            <span class="sys-heading-en">FREE OPTION</span>
            <span class="sys-heading-ja">無料オプション</span>
            --}}
            <span class="sys-heading-link"><a href="{{ route('options') }}" class="text-xs">&raquo; オプション詳細</a></span>
        </h3>
        @if($options->isNotEmpty())
        @php
            $optionsByCate = $options->groupBy('cate');
        @endphp

        {{-- カテゴリあり --}}
        @foreach($optionCates as $cate)
        @php $cateItems = $optionsByCate->get($cate->id, collect()); @endphp
        @if($cateItems->isNotEmpty())
        <div class="sys-option-group">
            <h4 class="sys-option-group-title">{{ $cate->title }}</h4>
            <ul class="sys-option-list">
                @foreach($cateItems->sortBy('sort') as $opt)
                <li class="sys-option-item">
                    <span class="sys-option-name">{{ $opt->title }}</span>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
        @endforeach

        {{-- カテゴリなし or cate=0 --}}
        @php $noCateItems = $optionsByCate->get(0, collect())->merge($optionsByCate->get(null, collect())); @endphp
        @if($noCateItems->isNotEmpty())
        <div class="sys-option-group">
            <ul class="sys-option-list">
                @foreach($noCateItems->sortBy('sort') as $opt)
                <li class="sys-option-item">
                    <span class="sys-option-name">{{ $opt->title }}</span>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
        @else
        <p class="sys-empty">現在、オプションの設定はありません。</p>
        @endif
    </section>

    {{-- ══════════════════════════════════════════════
         有料オプション
    ══════════════════════════════════════════════ --}}
    {{-- <section class="sys-section" id="sys-option-pay">
        <h3 class="sys-heading">
            <span class="sys-heading-en">PAID OPTION</span>
            <span class="sys-heading-ja">有料オプション</span>
        </h3>

        @if($option_pays->isNotEmpty())
        @php
            $paysByCate = $option_pays->groupBy('cate');
        @endphp

        @foreach($optionCates as $cate)
        @php $cateItems = $paysByCate->get($cate->id, collect()); @endphp
        @if($cateItems->isNotEmpty())
        <div class="sys-option-group">
            <h4 class="sys-option-group-title">{{ $cate->title }}</h4>
            <ul class="sys-option-list">
                @foreach($cateItems->sortBy('sort') as $opt)
                <li class="sys-option-item">
                    <span class="sys-option-name">{{ $opt->title }}</span>
                    @if($opt->price)
                    <span class="sys-option-price">+{{ number_format($opt->price) }}<small>円</small></span>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
        @endif
        @endforeach

        @php $noCateItems = $paysByCate->get(0, collect())->merge($paysByCate->get(null, collect())); @endphp
        @if($noCateItems->isNotEmpty())
        <div class="sys-option-group">
            <ul class="sys-option-list">
                @foreach($noCateItems->sortBy('sort') as $opt)
                <li class="sys-option-item">
                    <span class="sys-option-name">{{ $opt->title }}</span>
                    @if($opt->price)
                    <span class="sys-option-price">+{{ number_format($opt->price) }}<small>円</small></span>
                    @endif
                </li>
                @endforeach
            </ul>
        </div>
        @endif
        @else
        <p class="sys-empty">現在、有料オプションの設定はありません。</p>
        @endif
    </section> --}}

    {{-- ══════════════════════════════════════════════
         クレジットカードのご利用について
    ══════════════════════════════════════════════ --}}
    @if(!empty($shops->shop_payment))
    <section class="sys-section" id="sys-payment">
        <h3 class="sys-heading">
            <span class="sys-heading-en">CREDIT CARD</span>
            <span class="sys-heading-ja">クレジットカードのご利用について</span>
        </h3>
        <div class="sys-payment-body">
            <p>当店ではクレジットカードでのお支払いに対応しております。</p>
            <p>お電話でのご予約の際に、クレジットカードご利用の旨をスタッフまで必ずお伝えください。<br>
クレジット決済のご案内ページの注意事項をよくご確認いただきご利用くださいませ。</p>
            <p class="sys-payment-note">
                <i class="fas fa-shield-alt"></i>
                決済は SSL 暗号化通信により安全に処理されます。カード情報は当店では保持いたしません。
            </p>
            <a href="{{ $shops->shop_payment }}"
               target="_blank"
               rel="noopener noreferrer"
               class="sys-btn sys-btn-payment">
                <i class="fas fa-credit-card"></i> 決済ページへ
            </a>
        </div>
    </section>
    @endif

    {{-- ══════════════════════════════════════════════
         注意事項
    ══════════════════════════════════════════════ --}}
    <section class="sys-section" id="sys-notes">
        <h3 class="sys-heading">
            <span class="sys-heading-en">NOTES</span>
            <span class="sys-heading-ja">注意事項</span>
        </h3>

        {{-- 注意事項は旧サイト（_gotanda/www_tpl/inc_notes.tpl）からの静的コピー --}}
        <div class="sys-static-notes sys-static-notes--gotanda">
            <div>
            <h4>※無料オプションについて</h4>
            <p>〇オプションに関して、受付の際に必ずお伝えください。<br>
            即系、ノーパンノーブラ、撮影、ハードOP、持ち込み記載があるものに関して、事前にお伝えいただけないとご利用できない場合があります。</p>
            <p>〇オプションは、あくまで無料のものです。予期せぬ場合、オプションができずとも、女の子に強要する行為は控えるよう注意お願いいたします。<br>
            また、オプションができない等の理由で、チェンジ・キャンセルはできませんので、ご了承ください。コース料金全額の負担となります。</p>
            </div>
            
            <div>
            <h4>ご利用規約・禁止事項</h4>
            <p>当店では、キャストが安心・安全にサービスを提供できるよう、キャスト及び、当店スタッフに対して以下の行為を固く禁止しております。</p>
            
            <h5>禁止事項</h5>
            <ul>
            <li>本番行為（性交渉）および本番行為を要求・強要・示唆する発言</li>
            <li>暴力行為、暴言、脅迫、威圧的な言動</li>
            <li>キャストが嫌がる行為、無理なプレイの強要</li>
            <li>窃盗、盗撮、盗聴、録音、ライブ配信</li>
            <li>薬物使用後（キャスト及び、当店スタッフがそう判断した場合も含む）の利用及び、薬物の持ち込み</li>
            <li>泥酔状態（キャスト及び、当店スタッフがそう判断した場合も含む）でのご利用</li>
            <li>18歳未満の方のご利用</li>
            <li>他店への勧誘・引き抜き行為全て</li>
            <li>個人的な交際や連絡先交換の強要</li>
            <li>ストーカー行為や待ち伏せ</li>
            <li>キャストの身体を傷つける行為</li>
            <li>性感染症の疑いがある状態でのご利用</li>
            <li>暴力団関係者及びそれに準ずる方の利用</li>
            <li>その他、キャストまたは店舗が危険・迷惑と判断した行為</li>
            </ul>
            
            <p>〇非通知、公衆電話からの受付は一切行っておりませんのでご了承お願いいたします。</p>
            <p>〇電話予約、ヘブン予約、女の子を通しての予約確定後、いかなる場合でもコース変更、チェンジ、キャンセルはできませんのでご了承いただきます。（キャンセルされる方は100％負担となります）尚、悪質キャンセル（無断キャンセル、キャンセル料支払いの拒否）された場合、弁護士への相談その他の法的措置を講じます。その損害（営業損失、弁護士費用その他相当因果関係のある費用を含みます。）について、法令に基づき賠償を請求する場合があります。</p>
            </div>
            
            <div>
            <h5>禁止事項に該当した場合</h5>
            <p>禁止事項に該当する行為が確認された場合は、その時点でサービスを終了し、ご退室いただきます。なお、サービス料金の返金はいたしません。</p>
            <p>また、悪質な行為については、以後のご利用をお断りするとともに、必要に応じて警察への通報、弁護士への相談その他の法的措置を講じます。</p>
            <p>さらに、当店またはキャストに損害が発生した場合には、その損害（営業損失、治療費、弁護士費用その他相当因果関係のある費用を含みます。）について、法令に基づき賠償を請求する場合があります。</p>
            </div>
            
            <div>
            <h4>※撮影に関して</h4>
            <p>撮影コースで撮影・録音した動画や画像及び音声をインターネットにアップすることは、法律により禁止されております。</p>
            <p>当店での「インターネットにアップする」の定義として、</p>
            <ul>
            <li>動画サイトやブログへの投稿</li>
            <li>Ｘ、LINE、Facebook、Instagram等のSNSへの投稿</li>
            <li>メールに添付して送信すること</li>
            <li>その他、他者が閲覧できる状態にすること</li>
            </ul>
            <p>など、あらゆる形式でのインターネットへの掲載を指します。</p>
            <p>撮影・録音した動画や画像や音声につきましては、ご本人様限りでお楽しみいただきますようお願いいたします。</p>
            <p>また撮影のルールとして、<br>
            ①撮影を始める際はコンパニオンに通達したのち開始してください。<br>
            ②アイマスク限定の女の子もいますのでご確認ください,<br>
            ③撮影後コンパニオンから確認を取らせていただきます。</p>
            <p>お互いが楽しくプレイできるようにご協力お願いいたします。</p>
            </div>
        </div>

        {{-- ▼ 管理画面の注意事項（動的表示）は停止中。戻す場合は @if(false) を外す --}}
        @if(false)
        @if($notes->isNotEmpty())
        @foreach($notes as $note)
        <div class="sys-note-block">
            @if($note->title)
            <h4 class="sys-note-title">{{ $note->title }}</h4>
            @endif
            @if($note->subtitle)
            <h5 class="sys-note-subtitle">{{ $note->subtitle }}</h5>
            @endif
            @if($note->content)
            <div class="sys-note-content">{!! $note->content !!}</div>
            @endif
        </div>
        @endforeach
        @endif
        @endif{{-- /@if(false) --}}

    </section>
    
</div>{{-- /.sys-page --}}

@endsection
