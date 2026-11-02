# ファンクラブ機能 設計・実装メモ

Laravel 11 / PHP 8.2 / MySQL の既存予約システムに対して、キャスト単位の月額サブスク型ファンクラブ機能を追加するための設計・実装内容。

## 概要

- キャストごとに 1 つのファンクラブを持てる構成
- 会員はファンクラブに登録（決済はダミー実装）
- ステータス管理: `active / paused / canceled`
- 管理者が会員状態・支払い状態を手動更新可能
- 投稿型コンテンツ機能（カテゴリ / 投稿 / 投稿詳細アイテム）
- API 応答を意識し、`expectsJson()` 分岐を一部実装

---

## 1. マイグレーション

追加:

- `database/migrations/2026_03_27_120000_create_fanclub_feature_tables.php`

作成テーブル:

1. `fanclubs`
   - `id`, `cast_id`, `title`, `description`, `price`, `status`, `square_plan_id`, timestamps
2. `fanclub_memberships`
   - `id`, `fanclub_id`, `member_id`, `status`, `payment_method`, `payment_status`
   - `started_at`, `ended_at`, `last_paid_at`, `next_billing_at`
   - `square_customer_id`, `square_subscription_id`, `admin_note`, timestamps
3. `fanclub_contents_categories`
   - `id`, `cast_id`, `name`, `sort`, timestamps
4. `fanclub_contents`
   - `id`, `cast_id`, `category_id`, `title`, `description`, `is_paid`
   - `scheduled_at`, `next_release_at`, `published_at`, timestamps
5. `fanclub_content_items`
   - `id`, `content_id`, `type`, `value(json)`, `sort`, timestamps

---

## 2. モデル定義（リレーション）

追加モデル:

- `app/Models/Fanclub.php`
- `app/Models/FanclubMembership.php`
- `app/Models/FanclubContentCategory.php`
- `app/Models/FanclubContent.php`
- `app/Models/FanclubContentItem.php`

既存モデル更新:

- `app/Models/Item.php`
  - `fanclub()`
  - `fanclubCategories()`
  - `fanclubContents()`
- `app/Models/Member.php`
  - `fanclubMemberships()`

---

## 3. Service クラス（fat controller 回避）

### Fanclub ドメイン

- `app/Services/Fanclub/FanclubMembershipService.php`
  - 参加登録
  - 会員ステータス変更
  - 支払いステータス更新
- `app/Services/Fanclub/FanclubContentService.php`
  - 投稿作成
  - 投稿更新
  - 予約投稿の公開処理（`publishDueContents()`）

### 決済（将来 Square へ差し替え前提）

- `app/Services/Payment/SquarePaymentGatewayInterface.php`
- `app/Services/Payment/DummySquarePaymentGateway.php`
- `config/fanclub.php`
  - `FANCLUB_PAYMENT_DRIVER`
  - Square関連 env キーの受け口

バインド:

- `app/Providers/AppServiceProvider.php`
  - `SquarePaymentGatewayInterface` -> `DummySquarePaymentGateway`

---

## 4. Policy / 認可

追加:

- `app/Policies/FanclubPolicy.php`
- `app/Policies/FanclubContentPolicy.php`

登録:

- `app/Providers/AuthServiceProvider.php`
  - `Fanclub::class => FanclubPolicy::class`
  - `FanclubContent::class => FanclubContentPolicy::class`

---

## 5. コントローラー

### キャスト側

- `app/Http/Controllers/Item/FanclubController.php`
  - `edit`, `update`, `members`
- `app/Http/Controllers/Item/FanclubContentController.php`
  - `index`, `create`, `store`, `edit`, `update`, `destroy`, `publishNow`

### 会員側

- `app/Http/Controllers/Member/FanclubMembershipController.php`
  - `index`, `subscribe`, `updateStatus`
- `app/Http/Controllers/Member/FanclubContentController.php`
  - `show`

### 管理者側

- `app/Http/Controllers/Admin/FanclubMembershipController.php`
  - `index`, `update`

---

## 6. ルーティング

更新:

- `routes/item.php`
- `routes/member.php`
- `routes/admin.php`

主なルート名:

- キャスト:
  - `item.fanclub.edit`
  - `item.fanclub.update`
  - `item.fanclub.members`
  - `item.fanclub.contents.*`
- 会員:
  - `member.fanclubs.index`
  - `member.fanclubs.subscribe`
  - `member.fanclubs.membership.update`
  - `member.fanclubs.show`
- 管理:
  - `admin.fanclubs.memberships.index`
  - `admin.fanclubs.memberships.update`

---

## 7. 最低限ビュー

### キャスト

- `resources/views/item/fanclubs/edit.blade.php`
- `resources/views/item/fanclubs/members.blade.php`
- `resources/views/item/fanclub_contents/index.blade.php`
- `resources/views/item/fanclub_contents/create.blade.php`
- `resources/views/item/fanclub_contents/edit.blade.php`
- `resources/views/item/fanclub_contents/partials/form.blade.php`

### 会員

- `resources/views/member/fanclubs/index.blade.php`
- `resources/views/member/fanclubs/show.blade.php`

### 管理

- `resources/views/admin/fanclubs/memberships/index.blade.php`

---

## 8. 拡張性ポイント

- `fanclub_content_items.value` を JSON にすることで、`image/video/text/link/timer` 以外も追加しやすい
- Service 層で業務ロジックを隔離しており、決済や配信タイミング処理の差し替えが容易
- Policy 導入済みでロール増加時も認可を局所化できる
- `expectsJson()` 分岐により SPA/API 化への移行が容易
- `with()` による eager load を使い、N+1 を抑制

---

## 9. 将来追加想定（設計指針）

- いいね:
  - `fanclub_content_reactions`（`content_id`, `member_id`, `type`）
- コメント:
  - `fanclub_content_comments`（`content_id`, `member_id`, `body`, `parent_id`）
- DM:
  - 既存 Message 系とは分離し、fanclub ドメイン専用チャネルを追加検討
- ライブ配信連携:
  - `fanclub_live_streams` を別テーブルで追加し、`content_items` には埋め込みリンクのみ保持

---

## 10. 注意点

- 本実装時は `bootstrap/app.php` の route 読み込み構成（`item.php` 二重読み込み）を一度整理する
- 決済本番切替時は `DummySquarePaymentGateway` を実装クラスに置換
- 画像アップロード流用時は既存 `Storage::url()` 運用に合わせて `value` のキー設計を統一する

