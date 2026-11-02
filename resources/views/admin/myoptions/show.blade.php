<x-admin-layout>

<x-slot name="header">
 マイオプション詳細
</x-slot>

<section class="toPageArea">
    <a href="{{ route('admin.myoptions.cast', $myoption->item_id) }}" class="lists">一覧に戻る</a>
    <a href="{{ route('admin.myoptions.edit', $myoption) }}" class="edit">編集</a>
</section>

<section>
    <div class="detail-container">
        <div class="detail-row">
            <div class="detail-label">キャスト</div>
            <div class="detail-value">{{ $myoption->item->item_name }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">カテゴリ</div>
            <div class="detail-value">{{ $myoption->category ? $myoption->category->name : '未設定' }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">オプション名</div>
            <div class="detail-value">{{ $myoption->name }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">説明文</div>
            <div class="detail-value">{{ $myoption->body ?: 'なし' }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">画像</div>
            <div class="detail-value">
                @if ($myoption->filename)
                    <img src="{{ Storage::url('myoptions/' . $myoption->filename) }}" alt="" width="300">
                @else
                    なし
                @endif
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">表示設定</div>
            <div class="detail-value">
                @switch($myoption->active)
                    @case(0)
                        <span class="badge badge-danger">非表示</span>
                        @break
                    @case(1)
                        <span class="badge badge-success">表示</span>
                        @break
                    @default
                        <span class="badge badge-secondary">未設定</span>
                @endswitch
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">並び順</div>
            <div class="detail-value">{{ $myoption->sort }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">作成日時</div>
            <div class="detail-value">{{ $myoption->created_at->format('Y-m-d H:i:s') }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">更新日時</div>
            <div class="detail-value">{{ $myoption->updated_at->format('Y-m-d H:i:s') }}</div>
        </div>
    </div>
</section>

<style>
.detail-container {
    max-width: 800px;
    margin: 0 auto;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.detail-row {
    display: flex;
    border-bottom: 1px solid #eee;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    width: 150px;
    padding: 15px;
    background-color: #f8f9fa;
    font-weight: bold;
    color: #495057;
    border-right: 1px solid #eee;
}

.detail-value {
    flex: 1;
    padding: 15px;
    color: #333;
}

.badge {
    display: inline-block;
    padding: 0.25em 0.6em;
    font-size: 0.75em;
    font-weight: 700;
    line-height: 1;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: 0.25rem;
}

.badge-success {
    color: #fff;
    background-color: #28a745;
}

.badge-danger {
    color: #fff;
    background-color: #dc3545;
}

.badge-secondary {
    color: #fff;
    background-color: #6c757d;
}

.detail-value img {
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
</style>

</x-admin-layout>
