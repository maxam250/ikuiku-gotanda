# Square 決済設定マニュアル

> 対象バージョン：Square API `2025-10-16`
> 決済方式：**単発決済（Payments API）**
> Subscription API は使用しない

---

## 目次

1. [概要](#1-概要)
2. [Square ダッシュボードでの事前準備](#2-square-ダッシュボードでの事前準備)
3. [環境変数の設定](#3-環境変数の設定)
4. [動作確認（ドライバ切り替え）](#4-動作確認ドライバ切り替え)
5. [Webhook の設定](#5-webhook-の設定)
6. [本番切り替え手順](#6-本番切り替え手順)
7. [決済フロー全体図](#7-決済フロー全体図)
8. [トラブルシューティング](#8-トラブルシューティング)

---

## 1. 概要

本システムでは以下の2種類の単発決済を Square で処理する。

| 決済種別 | 説明 | `purchase_type` |
|---------|------|----------------|
| ファンクラブ参加 | キャスト単位の参加費（一回限り） | `fanclub_join` |
| ポイント購入 | ポイントパッケージの購入 | `point_purchase` |

**Square Subscription API は使用しない。**
すべての決済は `POST /v2/payments`（単発決済）で処理する。

---

## 2. Square ダッシュボードでの事前準備

### 2-1. アカウント作成・アプリケーション登録

1. [Square Developer](https://developer.squareup.com/) にログイン
2. 「My Applications」→「Create an Application」
3. アプリ名を入力して作成（例：`guide-fanclub`）

### 2-2. 取得が必要な認証情報

アプリ詳細ページの各タブから以下を取得する。

| 項目 | 場所 | 用途 |
|------|------|------|
| Application ID | Credentials タブ | Web Payments SDK の初期化 |
| Access Token | Credentials タブ（Production / Sandbox） | Payments API 呼び出し |
| Location ID | Locations タブ | 決済先ロケーション |

#### Sandbox（開発用）の場合
- 「Sandbox」タブに切り替えて取得
- URL: `https://connect.squareupsandbox.com`
- テストカード番号: `4111 1111 1111 1111`（有効期限: 任意未来日、CVV: 任意3桁）

#### Production（本番用）の場合
- 「Production」タブで取得
- URL: `https://connect.squareup.com`

### 2-3. Webhook の登録（後述の [5章](#5-webhook-の設定) も参照）

1. 「Webhooks」→「Subscriptions」→「Add Subscription」
2. URL に以下を入力：
   ```
   https://yourdomain.com/payment/webhook/square
   ```
3. 購読イベントで以下を選択：
   - `payment.created`
   - `payment.updated`
4. 保存後、**Signature Key** をコピーして控える

---

## 3. 環境変数の設定

`.env` に以下を追記する。

```dotenv
# ────────────────────────────────────────
# Square 決済設定
# ────────────────────────────────────────

# ドライバ切り替え
# dummy_square : ローカル開発用（Square API に接続しない）
# square       : 本番・ステージング用（Square API に接続する）
FANCLUB_PAYMENT_DRIVER=dummy_square

# Square アプリケーション ID（Web Payments SDK 初期化に使用）
SQUARE_APPLICATION_ID=

# Square アクセストークン（Payments API 呼び出しに使用）
SQUARE_ACCESS_TOKEN=

# 環境（sandbox または production）
SQUARE_ENVIRONMENT=sandbox

# ロケーション ID（Square ダッシュボード → Locations から取得）
SQUARE_LOCATION_ID=

# Webhook 署名キー（Square ダッシュボード → Webhooks から取得）
SQUARE_WEBHOOK_SIGNATURE_KEY=
```

### Sandbox 設定例

```dotenv
FANCLUB_PAYMENT_DRIVER=square
SQUARE_APPLICATION_ID=sandbox-sq0idb-xxxxxxxxxxxxxxxxxxxxxxxx
SQUARE_ACCESS_TOKEN=EAAAl...（Sandbox Access Token）
SQUARE_ENVIRONMENT=sandbox
SQUARE_LOCATION_ID=LXXXXXXXXXXXXXXXXX
SQUARE_WEBHOOK_SIGNATURE_KEY=v1:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

### Production 設定例

```dotenv
FANCLUB_PAYMENT_DRIVER=square
SQUARE_APPLICATION_ID=sq0idp-xxxxxxxxxxxxxxxxxxxxxxxx
SQUARE_ACCESS_TOKEN=EAAAl...（Production Access Token）
SQUARE_ENVIRONMENT=production
SQUARE_LOCATION_ID=LXXXXXXXXXXXXXXXXX
SQUARE_WEBHOOK_SIGNATURE_KEY=v1:xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

> ⚠️ `.env` は Git に含めないこと。`.env.example` にキー名だけ記載すること。

---

## 4. 動作確認（ドライバ切り替え）

### ダミードライバ（ローカル開発）

```dotenv
FANCLUB_PAYMENT_DRIVER=dummy_square
```

- Square API への通信を行わない
- カード入力不要（`source_id` に任意文字列を渡せばよい）
- 決済は常に `completed` を返す
- Webhook なしでも即時に参加/ポイントが反映される

### Square Sandbox（結合テスト）

```dotenv
FANCLUB_PAYMENT_DRIVER=square
SQUARE_ENVIRONMENT=sandbox
```

- Square のテスト環境に接続
- Square Developer のテストカードで決済テストが可能
- Webhook もテスト送信できる（Square ダッシュボード → Webhooks → Test Event）

---

## 5. Webhook の設定

### 受信 URL

```
POST https://yourdomain.com/payment/webhook/square
```

ローカル開発時は [ngrok](https://ngrok.com/) 等でトンネルして Square に公開 URL を登録する。

```bash
ngrok http 80
# → https://xxxx.ngrok.io/payment/webhook/square を Square に登録
```

### 署名検証の仕組み

Square は `x-square-hmacsha256-signature` ヘッダに HMAC-SHA256 署名を付与して送信する。

本システムでは以下の手順で検証する（`SquareWebhookController@verifySignature`）：

```
expected = BASE64( HMAC-SHA256( 通知URL + リクエストボディ, SQUARE_WEBHOOK_SIGNATURE_KEY ) )
```

**`SQUARE_WEBHOOK_SIGNATURE_KEY` が空の場合：**
- `APP_ENV=production` の環境では必ず認証失敗（401 を返す）
- それ以外の環境（local / staging）ではスキップされる（開発用の便宜）

### 購読イベント

| イベント | 用途 |
|---------|------|
| `payment.created` | 決済作成時（即時 COMPLETED の場合） |
| `payment.updated` | 決済ステータス更新時（PENDING → COMPLETED 等） |

上記以外のイベントは `SquareWebhookLog.status = 'skipped'` として無視される。

### 二重受信防止

- Square は同一イベントを複数回送信する場合がある
- `SquareWebhookLog.event_id` に UNIQUE 制約があるため、同一 `event_id` は2回目以降即 200 OK を返す
- さらに `PaymentLog.processed_at` が NULL でない場合は特典反映をスキップする

---

## 6. 本番切り替え手順

本番リリース時は以下の順番で設定する。

```
1. Square ダッシュボードで Production アプリの認証情報を取得
2. .env の値を Production 用に更新
   - FANCLUB_PAYMENT_DRIVER=square
   - SQUARE_APPLICATION_ID=（Production）
   - SQUARE_ACCESS_TOKEN=（Production）
   - SQUARE_ENVIRONMENT=production
   - SQUARE_LOCATION_ID=（Production）
3. Square ダッシュボードで Webhook URL を本番ドメインに登録
4. Signature Key を取得して SQUARE_WEBHOOK_SIGNATURE_KEY に設定
5. php artisan config:cache を実行
6. Square ダッシュボードの「Test Event」で疎通確認
```

---

## 7. 決済フロー全体図

```
【ファンクラブ参加フロー】

会員
  │
  ├─ GET /member/fanclubs/{id}/subscribe/confirm
  │     ↓ Square Web Payments SDK が読み込まれる
  │
  ├─ カード情報入力 → Square SDK がトークン化 → source_id 取得
  │
  ├─ POST /member/fanclubs/{id}/subscribe
  │     ↓ FanclubMembershipController@subscribe
  │     ↓ FanclubMembershipService@initiateJoin
  │     ├─ payment_logs 作成（status=pending）
  │     ├─ SquarePaymentGateway@createPayment（POST /v2/payments）
  │     ├─ payment_logs に square_payment_id を保存
  │     └─ status=completed なら即時 applyFanclubJoin()
  │
  └─ GET /member/fanclubs/{id}/subscribe/complete
        ↓ 完了ページ表示

【Webhook フロー（非同期）】

Square サーバー
  │
  ├─ POST /payment/webhook/square
  │     ↓ SquareWebhookController@handle
  │     ├─ 署名検証
  │     ├─ event_id で重複チェック（SquareWebhookLog）
  │     ├─ SquareWebhookLog に記録
  │     └─ ProcessSquareWebhookJob をキューに投入
  │
  └─ ProcessSquareWebhookJob@handle
        ├─ payment.status=COMPLETED のみ処理
        ├─ PaymentLog を square_payment_id で検索
        ├─ processed_at が NULL なら処理（二重防止）
        ├─ [fanclub_join]   → FanclubMembership を active に更新
        └─ [point_purchase] → members.point を加算 + MemberPoint 記録
```

---

## 8. トラブルシューティング

### 決済失敗時の確認手順

1. `payment_logs` テーブルで `status=failed` のレコードを確認
   ```sql
   SELECT * FROM payment_logs WHERE status = 'failed' ORDER BY created_at DESC LIMIT 10;
   ```
2. `error_message` カラムに Square のエラーメッセージが保存されている
3. Laravel ログ（`storage/logs/laravel.log`）で詳細を確認

### Webhook が処理されない場合

1. `square_webhook_logs` テーブルで受信状況を確認
   ```sql
   SELECT event_id, event_type, status, error_message FROM square_webhook_logs ORDER BY created_at DESC LIMIT 10;
   ```
2. `status=failed` の場合は `error_message` を確認
3. `SQUARE_WEBHOOK_SIGNATURE_KEY` が登録 URL と一致しているか確認
4. `payment_logs` の `processed_at` を確認（NULL = 未反映）

### ポイントが加算されない場合

1. `payment_logs` で該当の `square_payment_id` レコードを検索
2. `status` と `processed_at` を確認
3. `processed_at` が NULL → Webhook 未受信または処理失敗
4. Webhook ログで `event_type=payment.updated` のレコードを確認

### ローカルで Square Sandbox をテストする場合

```bash
# ngrok で公開 URL を作成
ngrok http 80

# 表示された URL を Square ダッシュボードの Webhook に登録
# 例: https://abcd1234.ngrok.io/payment/webhook/square

# Signature Key を .env に設定してから artisan config:cache
php artisan config:cache
```

---

## 関連ファイル一覧

| ファイル | 役割 |
|---------|------|
| `config/fanclub.php` | Square 設定の読み込み先 |
| `config/point_packages.php` | ポイントパッケージ定義 |
| `app/Services/Payment/SquarePaymentGateway.php` | Square API 実装 |
| `app/Services/Payment/DummySquarePaymentGateway.php` | ダミー実装（開発用） |
| `app/Services/Fanclub/FanclubMembershipService.php` | ファンクラブ参加処理 |
| `app/Services/Payment/PointPurchaseService.php` | ポイント購入処理 |
| `app/Http/Controllers/Api/SquareWebhookController.php` | Webhook 受信 |
| `app/Jobs/ProcessSquareWebhookJob.php` | Webhook 非同期処理 |
| `app/Models/PaymentLog.php` | 決済ログ（二重防止の基準） |
| `app/Http/Middleware/VerifyCsrfToken.php` | Webhook の CSRF 除外 |
