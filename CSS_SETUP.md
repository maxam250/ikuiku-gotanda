# CSS設定ガイド

## 概要

このプロジェクトでは、異なるディレクトリ（フロントエンド、admin、item、member）ごとに個別のCSSファイルを用意しています。

## CSSファイル構成

```
resources/css/
├── common.css           # Tailwind + 共通 @layer（各エントリから @import）
├── forms-app.css        # 会員・キャストダッシュ向けフォーム（Vite）
├── frontend/
│   ├── app.css          # 公開サイト Vite エントリ（layouts.app 等）
│   └── ranking.css      # ランキングページ単体エントリ
├── admin/
│   └── admin.css        # 管理画面 Vite エントリ
└── dashboard/
    ├── item.css         # キャストマイページ Vite エントリ
    └── member.css       # 会員マイページ Vite エントリ
```

## 各CSSファイルの使い方

### 1. フロントエンド（一般ユーザー向けページ）

**レイアウトファイル:** `resources/views/layouts/app.blade.php`

```php
@vite(['resources/css/frontend/app.css', 'resources/js/app.js'])
```

### 2. Admin（管理者画面）

**レイアウトファイル:** `resources/views/layouts/admin/app.blade.php`

```php
@vite(['resources/css/admin/admin.css', 'resources/js/app.js'])
```

**使用可能なクラス例:**
- `.admin-body` - ボディ全体
- `.admin-sidebar` - サイドバー
- `.admin-header` - ヘッダー
- `.admin-content` - メインコンテンツエリア
- `.admin-table` - データテーブル
- `.dashboard-card` - ダッシュボードカード

### 3. Item（キャスト管理画面）

**レイアウトファイル:** `resources/views/layouts/item/app.blade.php`

```php
@vite(['resources/css/dashboard/item.css', 'resources/js/app.js'])
```

**使用可能なクラス例:**
- `.item-body` - ボディ全体（ピンク/パープルグラデーション）
- `.item-sidebar` - サイドバー
- `.profile-card` - プロフィールカード
- `.gallery-grid` - ギャラリーグリッド
- `.schedule-card` - スケジュールカード
- `.booking-item` - 予約アイテム

### 4. Member（会員マイページ）

**レイアウトファイル:** `resources/views/layouts/member/app.blade.php`

```php
@vite(['resources/css/dashboard/member.css', 'resources/js/app.js'])
```

**使用可能なクラス例:**
- `.member-body` - ボディ全体（ブルー/インディゴグラデーション）
- `.member-sidebar` - サイドバー
- `.member-profile-card` - プロフィールカード
- `.point-card` - ポイントカード
- `.reservation-card` - 予約カード
- `.coupon-card` - クーポンカード
- `.favorite-grid` - お気に入りグリッド

## 共通スタイル（全てのCSSファイルに含まれる）

`admin/admin.css`、`dashboard/item.css`、`dashboard/member.css` および `frontend/app.css` は `common.css` を @import しているため、以下の共通クラスが使えます。

### フォーム関連
- `.form-input` - 入力フィールド
- `.form-textarea` - テキストエリア
- `.form-select` - セレクトボックス
- `.form-checkbox` / `.form-radio` - チェックボックス/ラジオボタン
- `.form-error` - エラーメッセージ
- `.form-help` - ヘルプテキスト

### ボタン
- `.btn` - 基本ボタン
- `.btn-primary` - プライマリボタン（ピンク）
- `.btn-secondary` - セカンダリボタン（グレー）
- `.btn-success` - 成功ボタン（緑）
- `.btn-danger` - 危険ボタン（赤）
- `.btn-outline` - アウトラインボタン

### カード
- `.card` - 基本カード
- `.card-header` - カードヘッダー
- `.card-body` - カード本文
- `.card-footer` - カードフッター

### テーブル
- `.table` - 基本テーブル
- `.table-striped` - ストライプテーブル

### バッジ
- `.badge` - 基本バッジ
- `.badge-primary` / `.badge-success` / `.badge-danger` など

### アラート
- `.alert` - 基本アラート
- `.alert-success` / `.alert-error` / `.alert-warning` / `.alert-info`

### モーダル
- `.modal-overlay` - モーダルオーバーレイ
- `.modal-content` - モーダルコンテンツ
- `.modal-header` / `.modal-body` / `.modal-footer`

## レイアウトファイルの設定例

### Admin用レイアウト例
```php
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>管理画面</title>
    @vite(['resources/css/admin/admin.css', 'resources/js/app.js'])
</head>
<body class="admin-body">
    <div class="admin-container">
        <!-- サイドバー -->
        <aside class="admin-sidebar">
            <div class="admin-sidebar-logo">
                管理画面
            </div>
            <nav class="admin-sidebar-nav">
                <a href="#" class="admin-sidebar-item admin-sidebar-item-active">
                    <i class="admin-sidebar-icon fas fa-home"></i>
                    <span>ダッシュボード</span>
                </a>
            </nav>
        </aside>

        <!-- メインコンテンツ -->
        <main class="admin-main">
            <header class="admin-header">
                <h1 class="admin-header-title">ダッシュボード</h1>
            </header>
            <div class="admin-content">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
```

### Item用レイアウト例
```php
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>キャスト管理</title>
    @vite(['resources/css/dashboard/item.css', 'resources/js/app.js'])
</head>
<body class="item-body">
    <div class="item-container">
        <!-- サイドバー -->
        <aside class="item-sidebar">
            <div class="item-sidebar-logo">
                キャスト管理
            </div>
            <nav class="item-sidebar-nav">
                <a href="#" class="item-sidebar-item item-sidebar-item-active">
                    <i class="item-sidebar-icon fas fa-user"></i>
                    <span>プロフィール</span>
                </a>
            </nav>
        </aside>

        <!-- メインコンテンツ -->
        <main class="item-main">
            <header class="item-header">
                <h1 class="item-header-title">マイページ</h1>
            </header>
            <div class="item-content">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
```

### Member用レイアウト例
```php
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>会員マイページ</title>
    @vite(['resources/css/dashboard/member.css', 'resources/js/app.js'])
</head>
<body class="member-body">
    <div class="member-container">
        <!-- サイドバー -->
        <aside class="member-sidebar">
            <div class="member-sidebar-logo">
                マイページ
            </div>
            <nav class="member-sidebar-nav">
                <a href="#" class="member-sidebar-item member-sidebar-item-active">
                    <i class="member-sidebar-icon fas fa-home"></i>
                    <span>ホーム</span>
                </a>
            </nav>
        </aside>

        <!-- メインコンテンツ -->
        <main class="member-main">
            <header class="member-header">
                <h1 class="member-header-title">マイページ</h1>
            </header>
            <div class="member-content">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
```

## 開発時の注意事項

1. **開発サーバーを起動してください:**
   ```bash
   npm run dev
   ```

2. **本番環境用にビルドする場合:**
   ```bash
   npm run build
   ```

3. **スタイルの変更:**
   - 共通のスタイルを変更する場合: `common.css`を編集
   - admin固有のスタイル: `admin.css`を編集
   - item固有のスタイル: `item.css`を編集
   - member固有のスタイル: `member.css`を編集

4. **Tailwind CSSの使用:**
   - すべてのCSSファイルでTailwind CSSの`@apply`ディレクティブが使用できます
   - HTML内で直接Tailwindクラスも使用可能です

## トラブルシューティング

### CSSが反映されない場合

1. npm run devが起動しているか確認
2. ブラウザのキャッシュをクリア
3. `npm run build`を実行して再ビルド

### 警告メッセージが出る場合

`@import must precede all other statements`という警告が出ても、機能には影響ありません。これはCSSの@importの順序に関する警告です。
