<x-admin-layout>

<x-slot name="header">
 マイコスチューム詳細
</x-slot>

<section class="toPageArea">
    <a href="{{ route('admin.mycostumes.index') }}" class="lists">一覧に戻る</a>
    <a href="{{ route('admin.mycostumes.edit', $mycostume) }}" class="edit">編集</a>
</section>

<section>
    <div class="detail-container">
        <div class="detail-row">
            <div class="detail-label">キャスト</div>
            <div class="detail-value">{{ $mycostume->item->item_name ?? '未設定' }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">カテゴリ</div>
            <div class="detail-value">{{ $mycostume->category->name ?? '未設定' }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">コス名</div>
            <div class="detail-value">{{ $mycostume->name }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">説明文</div>
            <div class="detail-value">{{ $mycostume->body ?: '説明文なし' }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">画像</div>
            <div class="detail-value">
                @if ($mycostume->filename)
                    <img src="{{ Storage::url('mycostumes/' . $mycostume->filename) }}" alt="" width="300" class="detail-image">
                @else
                    画像なし
                @endif
            </div>
        </div>

        <div class="detail-row">
            <div class="detail-label">表示設定</div>
            <div class="detail-value">
                @switch($mycostume->active)
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
            <div class="detail-value">{{ $mycostume->sort }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">作成日時</div>
            <div class="detail-value">{{ $mycostume->created_at->format('Y-m-d H:i:s') }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">更新日時</div>
            <div class="detail-value">{{ $mycostume->updated_at->format('Y-m-d H:i:s') }}</div>
        </div>
    </div>

    <div class="action-buttons">
        <a href="{{ route('admin.mycostumes.edit', $mycostume) }}" class="btn btn-warning">編集</a>
        <form action="{{ route('admin.mycostumes.destroy', $mycostume) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger" onclick="return confirm('本当に削除しますか？')">削除</button>
        </form>
    </div>
</section>

<style>
.detail-container {
    max-width: 800px;
    margin: 0 auto;
    background-color: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
}

.detail-row {
    display: flex;
    border-bottom: 1px solid #dee2e6;
    padding: 15px 0;
}

.detail-row:last-child {
    border-bottom: none;
}

.detail-label {
    width: 150px;
    font-weight: bold;
    color: #495057;
    flex-shrink: 0;
}

.detail-value {
    flex: 1;
    color: #212529;
}

.detail-image {
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 5px;
    background-color: white;
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

.action-buttons {
    margin-top: 30px;
    text-align: center;
}

.btn {
    display: inline-block;
    font-weight: 400;
    text-align: center;
    vertical-align: middle;
    user-select: none;
    border: 1px solid transparent;
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    line-height: 1.5;
    border-radius: 0.25rem;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    text-decoration: none;
    cursor: pointer;
    margin: 0 5px;
}

.btn-warning {
    color: #212529;
    background-color: #ffc107;
    border-color: #ffc107;
}

.btn-warning:hover {
    color: #212529;
    background-color: #e0a800;
    border-color: #d39e00;
}

.btn-danger {
    color: #fff;
    background-color: #dc3545;
    border-color: #dc3545;
}

.btn-danger:hover {
    color: #fff;
    background-color: #c82333;
    border-color: #bd2130;
}
</style>

</x-admin-layout>
