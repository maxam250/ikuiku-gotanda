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

    </section>
    
</div>{{-- /.sys-page --}}

@endsection
