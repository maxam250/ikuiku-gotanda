@extends('layouts.app')

@section('breadcrumb_label', '求人情報')
@section('content')

<div class="titleArea">
    <h2>RECRUIT</h2>
    <div class="title">求人情報</div>
</div>

<div class="rec-page">

    {{-- ナビゲーション --}}
    <nav class="rec-nav">
        <ul class="rec-nav-list">
            <li><a href="#firstArea"   class="rec-nav-link">はじめに</a></li>

            <li><a href="#faqArea"     class="rec-nav-link">よく聞かれる質問</a></li>
            <li><a href="#flowArea"    class="rec-nav-link">お仕事の流れ</a></li>
            <li><a href="#infoArea"    class="rec-nav-link">募集内容</a></li>
            <li><a href="#contactArea" class="rec-nav-link">応募・お問い合わせ</a></li>
        </ul>
    </nav>

    {{-- はじめに --}}
    <section id="firstArea" class="rec-section">
        <h3 class="rec-section-title">
            <picture>
                <source media="(max-width: 560px)" srcset="{{ asset('images/recruit/main.jpg') }}">
                <img src="{{ asset('images/recruit/main.jpg') }}" alt="キャスト募集" class="rec-main-img">
            </picture>
        </h3>
        <p class="rec-lead">
            当店は「ソフトサービス」なデリベリーヘルスのお店です！<br>

        </p>



    </section>


    {{-- よく聞かれる質問 --}}
    <section id="faqArea" class="rec-section">
        <h3 class="rec-section-title">よく聞かれる質問にお答えします</h3>
        <p class="rec-lead" style="margin-bottom:24px;">
            面接時や採用したキャストさんから聞かれたり、言われる感想をピックアップします！<br>
            当店について知っていただける参考になればと思います。
        </p>
        <dl class="rec-faq-list">
            <div class="rec-faq-item">
                <dt class="rec-faq-q">
                    <i class="fas fa-question-circle rec-faq-q-icon"></i>
                    こういう仕事はしたことがないのですが大丈夫ですか？
                </dt>
                <dd class="rec-faq-a">
                    こういった接客業は未経験のほうが多いです。<br>
                    このお仕事は”頑張った分だけ稼げる”ので夢や目標を叶える近道です！
                </dd>
            </div>
            <div class="rec-faq-item">
                <dt class="rec-faq-q">
                    <i class="fas fa-question-circle rec-faq-q-icon"></i>
                    お客さんと1対1で会うのは危険はないですか？
                </dt>
                <dd class="rec-faq-a">
                    当店を利用するお客様は、規約を厳守しておりますので安心してお仕事ができるよう何かあればスタッフが全力で対応いたします。<br>
                    キャストさんの安全は第一に考えておりますので安心してお仕事ができるよう何かあればスタッフが全力で対応いたします。
                </dd>
            </div>
            <div class="rec-faq-item">
                <dt class="rec-faq-q">
                    <i class="fas fa-question-circle rec-faq-q-icon"></i>
                    出勤、スケジュールの入れ方や変更などはどうしたらいいのですか？
                </dt>
                <dd class="rec-faq-a">
                    スケジュールの追加、変更、取り消しはお店にご連絡を頂ければいつでも対応可能です。<br>
                    予約の無い日は自由に変更頂けます。予約のある日に体調不良などによりキャンセルとなる場合は、スタッフがお客様へその旨コンタクトを取ります。
                </dd>
            </div>
            <div class="rec-faq-item">
                <dt class="rec-faq-q">
                    <i class="fas fa-question-circle rec-faq-q-icon"></i>
                    お客さんの要望は何でも聞かなくちゃダメなんですか？
                </dt>
                <dd class="rec-faq-a">
                    当店の「ご利用規約」に基づきお客様のハラスメント行為行為全般は禁止となっております。<br>
                    ただし好き嫌いといった個人的事由での拒否はよほどの理由が無い限りはNGでお願いしています。
                </dd>
            </div>
            <div class="rec-faq-item">
                <dt class="rec-faq-q">
                    <i class="fas fa-question-circle rec-faq-q-icon"></i>
                    待機している時間は時給はでますか？
                </dt>
                <dd class="rec-faq-a">
                    残念ながら待機時間は時給はお出ししておりません。<br>
                    待機時間や時間外でも写メ日記やSNSを更新してコンテンツを充実していくことでお客様が増えて行きます。
                </dd>
            </div>
            <div class="rec-faq-item">
                <dt class="rec-faq-q">
                    <i class="fas fa-question-circle rec-faq-q-icon"></i>
                    待機の時は何をしていればいいんですか？
                </dt>
                <dd class="rec-faq-a">
                    待機時は基本的に自由です。待機所で待機していたり、すぐに戻れる範囲なら自由にしていただいて結構です。待機時に写真の撮影やSNSの更新などを行っているキャストさんは予約につながりやすくなります。
                </dd>
            </div>
        </dl>
    </section>

    {{-- お仕事の流れ --}}
    <section id="flowArea" class="rec-section">
        <h3 class="rec-section-title">お仕事の流れ</h3>
        <div class="rec-flow-grid">
            <div class="rec-flow-item">
                <h4 class="rec-flow-item-title">1. スケジュールを提出</h4>
                <p>事前にスケジュールを提出して頂きます。予約が入っていなければいつでも変更可能です。</p>
            </div>
            <div class="rec-flow-item">
                <h4 class="rec-flow-item-title">2. スケジュールのある日</h4>
                <p>待機スペースで待機していただきます。</p>
            </div>
            <div class="rec-flow-item">
                <h4 class="rec-flow-item-title">3. 予約が入ったら</h4>
                <p>予約が入ったらお知らせします。時間と場所をお伝えするので向かってもらいます。</p>
            </div>
            <div class="rec-flow-item">
                <h4 class="rec-flow-item-title">4. お客様と合流</h4>
                <p>時間になりましたら合流してサービススタートとなります。</p>
            </div>
            <div class="rec-flow-item">
                <h4 class="rec-flow-item-title">5. 終了</h4>
                <p>終了の時間になったらその場でお別れとなります。</p>
            </div>
            <div class="rec-flow-item">
                <h4 class="rec-flow-item-title">6. 報酬のお支払い</h4>
                <p>後日、お仕事した分の報酬を1か月ごとにまとめてお渡しします。日払いにも対応しております。</p>
            </div>
        </div>
    </section>

    {{-- 募集内容 --}}
    <section id="infoArea" class="rec-section">
        <h3 class="rec-section-title">募集内容</h3>
        <div class="rec-table-wrap">
            <table class="rec-table">
                <tbody>
                    <tr>
                        <th>募集年齢</th>
                        <td>
                            18歳以上<br>
                            ※18歳以上でも高校生の方の応募は受け付けておりませんのでご了承ください。
                        </td>
                    </tr>
                    <tr>
                        <th>お仕事のシステム</th>
                        <td>
                            事前に出勤可能日を提出頂き、当日出勤してもらう形となります。<br>
                            出勤可能日は予約に対応出来るように必ず連絡が取れるようにしてもらいます。
                        </td>
                    </tr>
                    <tr>
                        <th>登録条件</th>
                        <td>
                            週１日からでもOKです。<br>
                            未経験の方も歓迎です。

                        </td>
                    </tr>
                    <tr>
                        <th>勤務時間</th>
                        <td>
                            営業時間の中でスケジュールを出していただき、出勤日に出勤して頂きます。
                        </td>
                    </tr>
                    <tr>
                        <th>所在地</th>
                        <td>
                            最寄り駅より徒歩3分の場所にあります。<br>
                            住所は面接時にお伝えいたします。
                        </td>
                    </tr>
                    <tr>
                        <th>給与</th>
                        <td>
                            完全成果報酬<br>
                            ご利用料金を既定のパーセンテージでバックいたします。<br>
                            <br>
                            詳細は面接時にご案内いたします。<br>
                            また希望やご提案がございましたらお気軽にご相談ください。
                        </td>
                    </tr>
                    <tr>
                        <th>応募の流れ</th>
                        <td>
                            応募専用のメールフォーム、またはLINEよりプロフィールとお写真・SNS等をお送りください。<br>
                            （※ページ下部をご覧ください）<br><br>
                            お送りいただいた情報を確認し、一次審査を行います。<br>
                            一次審査をパスされた方は面談の日程を調整し、後日ご来社頂き面談を行います。（要履歴書）<br><br>
                            遠方の方や、時間が取れない、その他ご事情のある方はオンラインでの面接も対応いたしますのでご相談下さい。<br><br>
                            面談後、採用とさせて頂きましたら、宣材写真の撮影やスケジュールなど打ち合わせをいたします。
                        </td>
                    </tr>
                    <tr>
                        <th>応募できない方</th>
                        <td>
                            下記の方はご応募をご遠慮頂いております。(採用後に発覚した場合でも退店して頂くことになります)<br><br>
                            ・自傷痕のある方<br>
                            ・違法薬物の使用歴のある方<br>
                            ・反社会的勢力に関与している方<br>
                            ・就労ビザ、就労資格のない外国籍の方<br>
                            ・未成年、高校生、高校生と同学年の方<br>
                            ・通年通して海外に在住の方
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

    {{-- 応募・お問い合わせ --}}
    <section id="contactArea" class="rec-section">
        <h3 class="rec-section-title">応募・お問い合わせ</h3>

        {{-- LINE --}}
        <article id="toLine">
            <h4 class="rec-contact-subtitle line-color">
                <i class="fab fa-line"></i> LINEから
            </h4>
            <p class="rec-lead">
                LINEから応募の方は、こちらのIDを友だち追加して頂き、
                <strong>バストアップ</strong>と<strong>全身を含む写真</strong>と、下記の項目をお送りください。
            </p>
            <div class="rec-line-wrap">
                <div class="rec-line-qr">
                    <a href="https://line.me/R/ti/p/" target="_blank" rel="noopener noreferrer">
                        友だち追加
                        <img src="images/recruit/line_qrcode.png" alt="LINE友達追加QRコード">
                    </a>
                </div>
                <div>
                    <ul class="rec-line-items">
                        <li>名前</li>
                        <li>年齢　生年月日</li>
                        <li>接客・配信等の経験</li>
                        <li>その他PR・質問・申告事項等</li>
                    </ul>
                </div>
            </div>
        </article>

        <hr class="rec-section-divider">

        {{-- メールフォーム --}}
        <article id="toForm">
            <h4 class="rec-contact-subtitle mail-color">
                <i class="fab fa-wpforms"></i> メールフォームから
            </h4>

            @if(session('success'))
                <div class="rec-flash-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="rec-flash-error">{{ session('error') }}</div>
            @endif

            <form action="{{ route('recruit.submit') }}" method="post" enctype="multipart/form-data" class="rec-form">
                @csrf

                <div class="rec-form-grid">
                    <div class="rec-form-group">
                        <label class="rec-form-label">お名前 <span class="req">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="rec-form-input">
                        @error('name')<p class="rec-form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="rec-form-group">
                        <label class="rec-form-label">会社名 <span class="note">※事務所関係の方</span></label>
                        <input type="text" name="company" value="{{ old('company') }}" class="rec-form-input">
                    </div>
                    <div class="rec-form-group">
                        <label class="rec-form-label">年齢 <span class="req">*</span></label>
                        <div class="rec-form-age-row">
                            <span>現在</span>
                            <input type="number" name="age" value="{{ old('age') }}" min="18" class="rec-form-input age-input" style="width:5rem">
                            <span>歳</span>
                        </div>
                        @error('age')<p class="rec-form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="rec-form-group">
                        <label class="rec-form-label">メールアドレス <span class="req">*</span></label>
                        <input type="email" name="mail" value="{{ old('mail') }}" class="rec-form-input">
                        @error('mail')<p class="rec-form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="rec-form-group">
                        <label class="rec-form-label">電話番号 <span class="note">（お電話でのお返事をご希望の方）</span></label>
                        <input type="tel" name="tel" value="{{ old('tel') }}" class="rec-form-input">
                    </div>
                    <div class="rec-form-group full-width">
                        <label class="rec-form-label">SNS・ブログまたはホームページ等 <span class="note">（SNSの場合アプリ名とIDでも可）</span></label>
                        <input type="text" name="url"  value="{{ old('url') }}"  placeholder="URLその1  or  twitter @moderukyujin123　(例)" class="rec-form-input" style="margin-bottom:8px;">
                        <input type="text" name="url2" value="{{ old('url2') }}" placeholder="URLその2  or  insta @moderukyujin123　(例)"  class="rec-form-input">
                    </div>
                </div>

                <div class="rec-radio-group">
                    <p class="rec-radio-group-title">経験</p>
                    <div class="rec-radio-options">
                        <label>
                            <input type="radio" name="exp" value="2" {{ old('exp', '2') == '2' ? 'checked' : '' }}>
                            なし
                        </label>
                        <label>
                            <input type="radio" name="exp" value="1" {{ old('exp') == '1' ? 'checked' : '' }}>
                            あり
                        </label>
                    </div>
                </div>

                <div class="rec-file-group">
                    <div class="rec-form-group">
                        <label class="rec-form-label">バストアップ写真 <span class="req">*</span></label>
                        <input type="file" name="attach1" accept="image/*" class="rec-form-file">
                        @error('attach1')<p class="rec-form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="rec-form-group">
                        <label class="rec-form-label">全身写真 <span class="req">*</span></label>
                        <input type="file" name="attach2" accept="image/*" class="rec-form-file">
                        @error('attach2')<p class="rec-form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="rec-form-group">
                        <label class="rec-form-label">その他の写真</label>
                        <input type="file" name="attach3" accept="image/*" class="rec-form-file">
                    </div>
                </div>

                <div class="rec-radio-group">
                    <p class="rec-radio-group-title">どちらで当店を知りましたか？</p>
                    <div class="rec-radio-options">
                        <label><input type="radio" name="media" value="0" {{ old('media', '0') == '0' ? 'checked' : '' }}> 未選択</label>
                        <label><input type="radio" name="media" value="1" {{ old('media') == '1' ? 'checked' : '' }}> ネット検索</label>
                        <label><input type="radio" name="media" value="2" {{ old('media') == '2' ? 'checked' : '' }}> 当グループ店</label>
                        <label><input type="radio" name="media" value="3" {{ old('media') == '3' ? 'checked' : '' }}> キャストブログ</label>
                        <label><input type="radio" name="media" value="4" {{ old('media') == '4' ? 'checked' : '' }}> SNS</label>
                        <label><input type="radio" name="media" value="5" {{ old('media') == '5' ? 'checked' : '' }}> 雑誌</label>
                        <label><input type="radio" name="media" value="6" {{ old('media') == '6' ? 'checked' : '' }}> 口コミ</label>
                        <label><input type="radio" name="media" value="7" {{ old('media') == '7' ? 'checked' : '' }}> その他</label>
                    </div>
                </div>

                <div class="rec-form-group" style="margin-bottom:20px;">
                    <label class="rec-form-label">その他PR・質問・申告事項等</label>
                    <textarea name="bikou" rows="6" class="rec-form-textarea">{{ old('bikou') }}</textarea>
                </div>

                <div class="rec-submit-wrap">
                    <button type="submit" class="rec-submit-btn">送信する</button>
                </div>
            </form>
        </article>
    </section>

</div>

@endsection
