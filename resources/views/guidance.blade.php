@extends('layouts.app')

@section('breadcrumb_label', 'ご利用ガイド')

@section('content')
<div class="titleArea">
    <h2>GUIDANCE</h2>
    <div class="title">ご利用ガイド</div>
</div>

<div class="guide-page">
    <div class="guide-tabs" role="tablist" aria-label="ご利用ガイドジャンル">
        <button class="guide-tab is-active" type="button" role="tab" aria-selected="true" aria-controls="guide-tab-panel-enjoy" id="guide-tab-enjoy">おさんぽプラン</button>
        <button class="guide-tab" type="button" role="tab" aria-selected="false" aria-controls="guide-tab-panel-online" id="guide-tab-online">オンライントークプラン</button>
        <button class="guide-tab" type="button" role="tab" aria-selected="false" aria-controls="guide-tab-panel-member" id="guide-tab-member">会員登録について</button>
        <button class="guide-tab" type="button" role="tab" aria-selected="false" aria-controls="guide-tab-panel-fanclub" id="guide-tab-fanclub">ファンクラブ</button>
        <button class="guide-tab" type="button" role="tab" aria-selected="false" aria-controls="guide-tab-panel-event" id="guide-tab-event">各種イベント</button>
    </div>

    <div class="guide-tab-panels">
        <section class="guide-tab-panel is-active" id="guide-tab-panel-enjoy" role="tabpanel" aria-labelledby="guide-tab-enjoy">
            <div class="guide-card">
                <h3>概要</h3>
                <p>おさんぽプランは、推しのキャストと秋葉原内でお散歩したりカフェでおしゃべりしたり、アミューズメントを一緒に楽しんだりできるプランです。<br>
                会員登録済みで、且つ3時間以上のご利用で秋葉原外のエリアでもご利用が可能となります。</p>
            </div>
            <div class="guide-card">
                <h3>できること</h3>
                <p>秋葉原エリア内でのお散歩、カフェ利用、アミューズメント施設やカラオケなどお好きなことをキャストといっしょに楽しめます。秋葉原ならではのマニアックなお店やコンカフェ巡りもおすすめです。</p>
            </div>
            <div class="guide-card">
                <h3>利用の流れ</h3>
                <p>スケジュール確認・予約 → ご来店と受付 → ご精算（事前清算可） → 待ち合わせ → ご利用開始</p>
                <p>希望日にスケジュールの出ていないキャストは「リクエスト予約」としてリクエストが可能です。</p>
                <a href="{{ route('schedule') }}">キャストスケジュールを見る</a>
            </div>
            <div class="guide-card">
                <h3>注意事項</h3>
                <p>ご利用規約の確認と同意が必要となります。<br>
                規約に抵触した場合、その時点で中止となります。その際返金はできませんので了承ください。<br>
                マナーやルールを守って楽しく健全ご利用ください。<br>
                ご利用内容でご不明な点がある場合はお気軽に受付までご相談ください。</p>
            </div>
        </section>

        <section class="guide-tab-panel" id="guide-tab-panel-online" role="tabpanel" aria-labelledby="guide-tab-online" hidden>
            <div class="guide-card">
                <h3>概要</h3>
                <p>オンライントークプランは、Google meetなどのビデオ通話アプリを用いてオンライン上でキャストとの時間を楽しめるプランです。秋葉原までは遠くて来られないという遠方の方でもご利用いただけます！</p>
            </div>
            <div class="guide-card">
                <h3>できること</h3>
                <p>時間いっぱい会話、応援メッセージ、企画トークなど、オンラインならではのコミュニケーションをお楽しみいただけます。</p>
            </div>
            <div class="guide-card">
                <h3>利用の流れ</h3>
                <p>希望日時の申請 → 受付からの確定連絡 → ご精算 → 通話URLなど詳細のご連絡 → 指定時間にトーク開始</p>
            </div>
            <div class="guide-card">
                <h3>注意事項</h3>
                <p>通信環境によって品質が変動します。録画・録音・スクリーンショット等の無断保存は禁止となります。<br>
                ご利用規約の確認と同意が必要となります。<br>
                規約に抵触した場合、その時点で中止となります。その際返金はできませんので了承ください。<br>
                マナーやルールを守って楽しく健全ご利用ください。<br>
                ご利用内容でご不明な点がある場合はお気軽に受付までご相談ください。</p>
            </div>
        </section>

        <section class="guide-tab-panel" id="guide-tab-panel-member" role="tabpanel" aria-labelledby="guide-tab-member" hidden>
            <div class="guide-card">
                <h3>概要</h3>
                <p>会員登録は、サービスの継続利用やエリア外利用に必要な登録手続きです。登録料は無料で、オンラインからお手続きできます。また会員の方には利用時にポイント付与など特典がございます。</p>
            </div>
            <div class="guide-card">
                <h3>特典・できること</h3>
                <p>会員マイページの利用、利用履歴の管理、対象特典の案内を受けられます。キャストのファンクラブに入会、利用する際には会員登録が必要となります。<br>
                [特典]<br>
                ・ポイント付与<br>
                ・ファンクラブ入会権<br>
                ・予約時の入力補助<br>
                ・ファンクラブ会員限定イベント・企画への参加<br>
                今後のキャンペーン情報も受け取りやすくなります。</p>
            </div>
            <div class="guide-card">
                <h3>登録の流れ</h3>
                <p>オンライン受付で申込 → 必要事項の記入と本人確認 → 会員証発行 → 次回以降のご利用時に会員情報を利用、となります。</p>
                <p><a href="{{ route('member.register')}}">会員登録</a></p>
            </div>
            <div class="guide-card">
                <h3>注意事項</h3>
                <p>登録情報に変更があった場合は速やかにお申し出ください。マイページ上から変更できる内容もございます。<br>
                規約違反が確認された場合、会員資格を停止することがあります。</p>
            </div>
        </section>

        <section class="guide-tab-panel" id="guide-tab-panel-fanclub" role="tabpanel" aria-labelledby="guide-tab-fanclub" hidden>
            <div class="guide-card">
                <h3>概要</h3>
                <p>ファンクラブは、推しキャストを応援できる会員向けコンテンツです。キャストが自身で写真や動画など限定コンテンツの投稿や限定企画を閲覧できます。<br>グッズ販売やライブ配信なども今後対応していく予定です。</p>
            </div>
            <div class="guide-card">
                <h3>できること</h3>
                <p>限定記事の閲覧、特典付きコンテンツの参加、メンバー向けお知らせの確認ができます。キャスト活動をより深く楽しめます。</p>
            </div>
            <div class="guide-card">
                <h3>利用の流れ</h3>
                <p>ファンクラブ一覧から対象を選択 → プラン内容を確認 → 参加手続き → マイページからコンテンツ利用、の流れです。</p>
                <a href="{{ route('fanclub') }}">ファンクラブ一覧を見る</a>
            </div>
            <div class="guide-card">
                <h3>注意事項</h3>
                <p>会員限定情報の転載・共有は禁止です。プラン内容や更新日は各ファンクラブページの記載をご確認ください。</p>
            </div>
        </section>

        <section class="guide-tab-panel" id="guide-tab-panel-event" role="tabpanel" aria-labelledby="guide-tab-event" hidden>
            <div class="guide-card">
                <h3>概要</h3>
                <p>イベントは、カフェなどの来店企画やオンライン型、また浴衣やテーマ衣装イベントやブロマイド配布といったサービスに付随するユーザー参加を前提とした企画です。季節企画や特別催事を開催します。</p>
            </div>
            <div class="guide-card">
                <h3>できること</h3>
                <p>企画参加、限定交流、特典配布対象企画への応募など、通常プランとは異なる体験をお楽しみいただけます。</p>
            </div>
            <div class="guide-card">
                <h3>利用の流れ</h3>
                <p>開催情報の確認 → 参加申込 → 受付案内の受領 → 当日参加、の順です。詳細はイベントごとの案内をご確認ください。</p>
            </div>
            <div class="guide-card">
                <h3>注意事項</h3>
                <p>募集人数に達し次第受付終了となります。開催条件の変更・中止時は、サイトまたは登録連絡先でご案内します。</p>
            </div>
        </section>
    </div>

    <div class="guide-contact-box">
        <p>ご不明な点がございましたら受付までお問い合わせください。</p>
        <a href="{{ route('contact') }}" class="guide-contact-btn">お問い合わせフォーム &rsaquo;&rsaquo;</a>
    </div>

</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = Array.from(document.querySelectorAll('.guide-tab'));
        const panels = Array.from(document.querySelectorAll('.guide-tab-panel'));

        tabs.forEach((tab) => {
            tab.addEventListener('click', function () {
                const targetId = tab.getAttribute('aria-controls');
                const targetPanel = document.getElementById(targetId);

                tabs.forEach((item) => {
                    item.classList.remove('is-active');
                    item.setAttribute('aria-selected', 'false');
                });

                panels.forEach((panel) => {
                    panel.classList.remove('is-active');
                    panel.setAttribute('hidden', 'hidden');
                });

                tab.classList.add('is-active');
                tab.setAttribute('aria-selected', 'true');
                if (targetPanel) {
                    targetPanel.classList.add('is-active');
                    targetPanel.removeAttribute('hidden');
                }
            });
        });
    });
</script>
@endsection
