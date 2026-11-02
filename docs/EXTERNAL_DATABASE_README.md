# 外部サーバー接続機能

このプロジェクトでは、予約フォームからの登録を外部サーバーのadmin_projectのreservationsテーブルに保存する機能を実装しています。

## 機能概要

- ローカルサーバーと外部サーバーの切り替えが可能
- 環境変数で簡単に設定変更
- 予約フォーム（フロントエンド）と管理ページの両方で外部サーバー接続に対応
- 他のプロジェクトでも流用可能な設計

## 設定方法

### 1. 環境変数の設定

`.env`ファイルに以下の設定を追加してください：

```env
# 外部サーバー接続設定
USE_EXTERNAL_DATABASE=false

# 外部サーバーのデータベース接続設定
EXTERNAL_DB_HOST=外部サーバーのホスト名
EXTERNAL_DB_PORT=3306
EXTERNAL_DB_DATABASE=admin_project
EXTERNAL_DB_USERNAME=データベースユーザー名
EXTERNAL_DB_PASSWORD=データベースパスワード
EXTERNAL_DB_CHARSET=utf8mb4
EXTERNAL_DB_COLLATION=utf8mb4_unicode_ci
EXTERNAL_MYSQL_ATTR_SSL_CA=
```

### 2. 外部サーバー接続の有効化

以下のコマンドで外部サーバー接続を有効にします：

```bash
php artisan external:switch external
```

ローカルサーバーに戻す場合：

```bash
php artisan external:switch local
```

### 3. 接続テスト

外部サーバー接続をテストするには：

```bash
php artisan external:test-connection
```

## 使用方法

### 予約フォームからの登録

予約フォームからの登録は自動的に設定されたサーバー（ローカルまたは外部）に保存されます。

### 管理ページでの操作

管理ページでの予約の作成、編集、削除も設定されたサーバーで実行されます。

## 実装されたファイル

### 設定ファイル
- `config/database.php` - 外部サーバー接続設定を追加

### モデル
- `app/Models/ExternalReservation.php` - 外部サーバー用予約モデル
- `app/Models/ExternalMember.php` - 外部サーバー用会員モデル
- `app/Models/ExternalItem.php` - 外部サーバー用アイテムモデル
- `app/Models/ExternalCourse.php` - 外部サーバー用コースモデル
- `app/Models/ExternalEquipment.php` - 外部サーバー用機器モデル
- `app/Models/ExternalSchedule.php` - 外部サーバー用スケジュールモデル

### サービス
- `app/Services/ExternalDatabaseService.php` - 外部サーバー接続管理サービス

### コントローラー（更新済み）
- `app/Http/Controllers/ReservationController.php` - フロントエンド予約コントローラー
- `app/Http/Controllers/Admin/ReservationController.php` - 管理画面予約コントローラー
- `app/Http/Controllers/Admin/EditReservationController.php` - 管理画面編集コントローラー

### コマンド
- `app/Console/Commands/TestExternalConnection.php` - 接続テストコマンド
- `app/Console/Commands/SwitchDatabase.php` - データベース切り替えコマンド

## トラブルシューティング

### 接続エラーが発生する場合

1. 外部サーバーの設定を確認
   - ホスト名、ポート番号
   - データベース名、ユーザー名、パスワード
   - ファイアウォール設定

2. 接続テストを実行
   ```bash
   php artisan external:test-connection
   ```

3. 設定キャッシュをクリア
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

### 予約が保存されない場合

1. 現在の接続設定を確認
   ```bash
   php artisan tinker
   >>> App\Services\ExternalDatabaseService::isExternalEnabled()
   ```

2. 外部サーバーのreservationsテーブルが存在することを確認

3. 外部サーバーのデータベースユーザーに適切な権限があることを確認

## 他のプロジェクトでの流用

この機能を他のプロジェクトで使用する場合：

1. 設定ファイル（`config/database.php`）の外部サーバー接続設定をコピー
2. 外部サーバー用モデル（`External*`）をコピー
3. `ExternalDatabaseService`をコピー
4. コントローラーで`ExternalDatabaseService`を使用するように修正
5. 環境変数を設定

## 注意事項

- 外部サーバー接続を使用する場合は、適切なセキュリティ設定を行ってください
- 本番環境ではSSL接続の使用を推奨します
- 定期的に接続テストを実行して、外部サーバーの状態を確認してください 