# Back to List Component

## 使用方法

一覧に戻るリンクの共通コンポーネントです。白背景にグレー枠、ややラウンドのデザインで統一されています。

### 基本的な使用

```blade
<x-back-to-list href="{{ route('gallery.index') }}" />
```

### カスタムテキストを使用

```blade
<x-back-to-list href="{{ route('items.index') }}" text="モデル一覧に戻る" />
```

### 各ページでの使用例

#### ギャラリー詳細ページ

```blade
<x-back-to-list href="{{ route('gallery.index') }}" />
```

#### アイテム詳細ページ

```blade
<x-back-to-list href="{{ route('items.index') }}" text="モデル一覧に戻る" />
```

#### ニュース詳細ページ

```blade
<x-back-to-list href="{{ route('news.index') }}" text="ニュース一覧に戻る" />
```

#### 会員ページ

```blade
<x-back-to-list href="{{ route('member.gallery.index') }}" text="マイギャラリーに戻る" />
```

## Props

- `href` (必須): 戻り先の URL
- `text` (オプション): 表示テキスト（デフォルト: "一覧に戻る"）

## デザイン特徴

- 白背景（white）
- グレー枠（gray-300）
- ややラウンド（rounded-lg）
- ホバー時に軽く上に移動するアニメーション
- 左矢印アイコン付き

# ヒントモーダル
{{-- 同一ページで複数使う例 --}}
```blade
<x-hint-modal id="hint-name" title="お名前について">
    フルネームをご入力ください。
</x-hint-modal>
```
```blade
<x-hint-modal id="hint-tel" title="電話番号について">
    ハイフンなしでご入力ください。
</x-hint-modal>
```