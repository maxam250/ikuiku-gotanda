# Mailgun Webhook 非会員メッセージシステム仕様書

## 概要

本システムは、Mailgun Webhook を利用して非会員（`member_id = 0`）のメール返信を自動的にデータベースに保存し、管理画面でメッセージのやり取りを可能にするシステムです。

## システム構成

### 1. データベース構造

#### guest_messages テーブル

```sql
CREATE TABLE guest_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email TEXT NOT NULL COMMENT '暗号化されたメールアドレス',
    sender_type VARCHAR(255) NOT NULL COMMENT 'admin or guest',
    message TEXT NOT NULL,
    title VARCHAR(255) NULL,
    image_path VARCHAR(255) NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    reservation_id BIGINT UNSIGNED NULL COMMENT '関連する予約ID',
    mailgun_message_id VARCHAR(255) NULL COMMENT 'Mailgunメッセージ ID',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,

    INDEX idx_email_prefix (email(100)),
    INDEX (reservation_id),
    INDEX (created_at),
    INDEX (mailgun_message_id)
);
```

### 2. 関連モデル

#### GuestMessage モデル

- **ファイルパス**: `app/Models/GuestMessage.php`
- **機能**:
  - メールアドレスの自動暗号化
  - 予約情報との関連付け
  - メールアドレスでの検索機能

### 3. コントローラー

#### MailgunWebhookController

- **ファイルパス**: `app/Http/Controllers/MailgunWebhookController.php`
- **エンドポイント**: `POST /webhooks/mailgun`
- **機能**:
  - Mailgun 署名検証
  - 会員/非会員の自動判定
  - メッセージの自動保存

#### MessageController (拡張)

- **ファイルパス**: `app/Http/Controllers/Admin/MessageController.php`
- **新規メソッド**:
  - `showGuest($email)`: 非会員メッセージ表示
  - `storeGuest(Request $request)`: 非会員へのメッセージ送信

## API 仕様

### Webhook エンドポイント

#### POST /webhooks/mailgun

**概要**: Mailgun からの受信メールを処理

**リクエストパラメータ**:

```
sender: string (送信者メールアドレス)
subject: string (件名)
body-plain: string (プレーンテキスト本文)
body-html: string (HTML本文)
Message-Id: string (MailgunメッセージID)
timestamp: string (タイムスタンプ)
token: string (トークン)
signature: string (署名)
```

**レスポンス**:

- 成功: `200 OK`
- 署名検証失敗: `401 Unauthorized`
- エラー: `500 Internal Server Error`

**処理フロー**:

1. 署名検証
2. 送信者メールアドレスで会員検索
3. 会員の場合: `messages`テーブルに保存
4. 非会員の場合: `guest_messages`テーブルに保存
5. 関連予約の自動検索・関連付け

### 管理画面 API

#### GET /admin/messages/guest/{email}

**概要**: 非会員メッセージ表示

**パラメータ**:

- `email`: URL エンコードされたメールアドレス

**レスポンス**: 非会員メッセージ表示画面

#### POST /admin/messages/guest

**概要**: 非会員へのメッセージ送信

**リクエストパラメータ**:

```
email: string (required) - 送信先メールアドレス
message: string (required, max:1000) - メッセージ本文
title: string (nullable, max:255) - 件名
image: file (nullable) - 添付画像
```

## 設定手順

### 1. 環境変数設定

`.env`ファイルに以下を追加:

```env
# Mailgun設定
MAILGUN_DOMAIN=your-domain.com
MAILGUN_SECRET=your-mailgun-api-key
MAILGUN_ENDPOINT=https://api.mailgun.net

# Webhook署名キー
MAILGUN_WEBHOOK_SIGNING_KEY=your-webhook-signing-key
```

### 2. Laravel 設定

`config/services.php`に追加:

```php
'mailgun' => [
    'domain' => env('MAILGUN_DOMAIN'),
    'secret' => env('MAILGUN_SECRET'),
    'endpoint' => env('MAILGUN_ENDPOINT', 'https://api.mailgun.net'),
    'webhook_signing_key' => env('MAILGUN_WEBHOOK_SIGNING_KEY'),
],
```

### 3. Mailgun Routes 設定

Mailgun 管理画面で以下のルートを設定:

```
Priority: 1
Filter: catch_all()
Actions:
- forward("https://yourdomain.com/webhooks/mailgun")
- store(notify="https://yourdomain.com/webhooks/mailgun")
```

### 4. マイグレーション実行

```bash
php artisan migrate
```

## 使用方法

### 管理者側操作

1. **予約一覧からアクセス**

   - 予約一覧画面で非会員の「メッセージ」ボタンをクリック
   - 自動的に非会員メッセージ画面に遷移

2. **メッセージ送信**

   - 件名、本文を入力
   - 必要に応じて画像を添付
   - 送信ボタンをクリック

3. **テンプレート使用**
   - 既存のメッセージテンプレートを選択
   - 「テンプレートを適用」ボタンで自動入力

### 非会員側操作

1. **メール受信**

   - システムからのメールを受信

2. **返信**
   - 受信メールに直接返信
   - 返信内容が自動的にシステムに保存

## セキュリティ機能

### 1. Webhook 署名検証

```php
private function verifyWebhookSignature(Request $request)
{
    $signingKey = config('services.mailgun.webhook_signing_key');
    $timestamp = $request->input('timestamp');
    $token = $request->input('token');
    $signature = $request->input('signature');

    $expectedSignature = hash_hmac('sha256', $timestamp . $token, $signingKey);
    return hash_equals($expectedSignature, $signature);
}
```

### 2. メールアドレス暗号化

- 全てのメールアドレスは Laravel の暗号化機能で保存
- `Crypt::encryptString()` / `Crypt::decryptString()` を使用

### 3. CSRF 保護

- 管理画面の全フォームで CSRF 保護を実装

## エラーハンドリング

### 1. Webhook エラー

- 署名検証失敗: ログ記録 + 401 レスポンス
- 処理エラー: ログ記録 + 500 レスポンス
- 不正なデータ: ログ記録 + 継続処理

### 2. メール送信エラー

- 送信失敗時もメッセージは DB 保存
- エラーログ記録
- ユーザーには成功メッセージ表示

## ログ出力

### 1. Webhook 受信ログ

```php
Log::info('Mailgun webhook received', [
    'sender' => $senderEmail,
    'subject' => $subject,
    'message_id' => $messageId
]);
```

### 2. メッセージ保存ログ

```php
Log::info('Message saved to guest_messages table', [
    'email' => $senderEmail
]);
```

## パフォーマンス考慮事項

### 1. インデックス最適化

- `email`カラムに部分インデックス（100 文字）
- `reservation_id`, `created_at`, `mailgun_message_id`にインデックス

### 2. メッセージ表示制限

- 最新 50 件に制限して表示
- ページネーション未実装（必要に応じて追加）

## 今後の拡張予定

### 1. 添付ファイル対応

- Webhook での添付ファイル処理
- ファイルサイズ制限
- ウイルススキャン

### 2. メッセージ検索機能

- 全文検索
- 日付範囲検索
- ステータス検索

### 3. 通知機能強化

- リアルタイム通知
- メール以外の通知方法（SMS 等）

## トラブルシューティング

### 1. Webhook が動作しない

- Mailgun Routes 設定確認
- 署名キー設定確認
- ログファイル確認

### 2. メールが送信されない

- Mailgun 設定確認
- 送信制限確認
- ログファイル確認

### 3. 文字化け

- データベース文字コード確認（utf8mb4）
- メール文字コード確認

## 関連ファイル一覧

### モデル

- `app/Models/GuestMessage.php`

### コントローラー

- `app/Http/Controllers/MailgunWebhookController.php`
- `app/Http/Controllers/Admin/MessageController.php` (拡張)

### ビュー

- `resources/views/admin/messages/show_guest.blade.php`
- `resources/views/emails/guest_message_notification.blade.php`

### マイグレーション

- `database/migrations/2025_09_28_133502_create_guest_messages_table.php`

### ルート

- `routes/web.php` (Webhook 用)
- `routes/admin.php` (管理画面用)

### メール

- `app/Mail/GuestMessageNotification.php`

---

**作成日**: 2025 年 9 月 28 日  
**バージョン**: 1.0  
**最終更新**: 2025 年 9 月 28 日
