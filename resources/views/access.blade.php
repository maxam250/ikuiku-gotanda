@extends('layouts.app')

@section('content')
<div class="titleArea">
<h2>ACCESS</h2>
<div class="title">アクセス</div>
</div>

<div class="access-section">

    <!-- Google Maps -->
    <div class="access-map">
        <iframe src="{{ $shops->shop_map }}" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>

    <!-- 住所 -->
    <div class="access-address">
        <h3 class="access-address-title">住所</h3>
        <p class="access-address-text">
            〒{{ $shops->shop_zip1 }}-{{ $shops->shop_zip2 }}<br>
            {{ $shops->shop_address }}<br>
            {{ $shops->shop_address2 }}
        </p>
    </div>

    <!-- 駅からのアクセス -->
    <div class="access-guide-section">
        <h4 class="access-guide-heading">駅からのアクセス</h4>

        <div class="access-station-grid">

            <!-- JR/日比谷線 秋葉原駅 -->
            <div class="access-station-card">
                <div class="access-station-header">
                    <span class="access-station-number">1</span>
                    <h5 class="access-station-name">JR/日比谷線 秋葉原駅</h5>
                </div>
                <ul class="access-station-steps">
                    <li>電気街口を出て直進すると中央通りにでます。</li>
                    <li>中央通りを右手「上野方面」へ直進し、UFJ銀行のある交差点を左折します。</li>
                    <li>セブンイレブンのある次の交差点を道なりに左折します。</li>
                    <li>30ｍ程行くと左手に弁当屋「やよい軒」があります。<br>同ビルの1階奥に進み、インターフォンで「601」を押してください。</li>
                </ul>
            </div>

            <!-- 銀座線 末広町駅 -->
            <div class="access-station-card">
                <div class="access-station-header">
                    <span class="access-station-number">2</span>
                    <h5 class="access-station-name">銀座線 末広町駅</h5>
                </div>
                <ul class="access-station-steps">
                    <li>3番出口を降り、蔵前橋通りを「湯島・御茶ノ水方面」に直進します。</li>
                    <li>セブンイレブンのある次の交差点を道なりに左折します。</li>
                    <li>30ｍ程行くと左手に弁当屋「やよい軒」があります。<br>同ビルの1階奥に進み、インターフォンで「601」を押してください。</li>
                </ul>
            </div>

            <!-- 千代田線 湯島駅 -->
            <div class="access-station-card">
                <div class="access-station-header">
                    <span class="access-station-number">3</span>
                    <h5 class="access-station-name">千代田線 湯島駅</h5>
                </div>
                <ul class="access-station-steps">
                    <li>6番出口を降り、左手に直進し昌平橋通りを蔵前橋通り、秋葉原方面に直進します。</li>
                    <li>セブンイレブンのある次の交差点を渡り直進します。</li>
                    <li>30ｍ程行くと左手に弁当屋「やよい軒」があります。<br>同ビルの1階奥に進み、インターフォンで「601」を押してください。</li>
                </ul>
            </div>

        </div>
    </div>

    <!-- お問い合わせ -->
    <div class="access-contact">
        <h5 class="access-contact-title">お問い合わせ</h5>
        <p class="access-contact-desc">ご不明な点がございましたら、お気軽にお電話ください。</p>
        <p class="access-contact-tel">{{ $shops->shop_tel }}</p>
        <p class="access-contact-hours">営業時間: {{ $shops->shop_open }}</p>
    </div>

</div>
@endsection
