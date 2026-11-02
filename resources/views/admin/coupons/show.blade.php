<x-admin-layout>

<x-slot name="header">
クーポン詳細
</x-slot>

<div class="coupon-show-container">
    <!-- 戻るボタン -->
    <div class="back-button-area">
        <a href="{{ route('admin.coupons.index') }}" class="admin-btn admin-btn-info">
            <i class="fas fa-arrow-left"></i>一覧に戻る
        </a>
    </div>

    <!-- メインカード -->
    <div class="coupon-detail-card">
        <!-- ヘッダーセクション -->
        <div class="coupon-header-section">
            <div class="coupon-title-area">
                <div class="coupon-title">{{ $coupon->title }}</div>
                <div class="coupon-status-badge">
                    @if($coupon->is_active)
                        <span class="status-badge status-active">
                            <i class="fas fa-check-circle"></i>有効
                        </span>
                    @else
                        <span class="status-badge status-inactive">
                            <i class="fas fa-times-circle"></i>無効
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- 画像セクション -->
        @if($coupon->main_image)
        <div class="coupon-image-section">
            <div class="image-wrapper">
                <img src="{{ Storage::url($coupon->main_image) }}" alt="クーポン画像" class="coupon-main-image">
            </div>
        </div>
        @endif

        <!-- 基本情報セクション -->
        <div class="coupon-info-section">
            <h3 class="section-title">
                <i class="fas fa-info-circle"></i>基本情報
            </h3>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-tag"></i>クーポンコード
                    </div>
                    <div class="info-value">
                        @if($coupon->code)
                            <span class="code-badge">{{ $coupon->code }}</span>
                        @else
                            <span class="text-muted">未設定</span>
                        @endif
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-calendar-alt"></i>有効期限
                    </div>
                    <div class="info-value">
                        @if($coupon->expires_at)
                            <span>{{ $coupon->expires_at->format('Y年m月d日 H:i') }}</span>
                        @else
                            <span class="text-muted">無期限</span>
                        @endif
                    </div>
                </div>
                
                <div class="info-item full-width">
                    <div class="info-label">
                        <i class="fas fa-align-left"></i>説明
                    </div>
                    <div class="info-value">
                        <p class="description-text">{{ $coupon->description ?: '説明が設定されていません' }}</p>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-clock"></i>作成日時
                    </div>
                    <div class="info-value">
                        <span>{{ $coupon->created_at->format('Y年m月d日 H:i') }}</span>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-edit"></i>更新日時
                    </div>
                    <div class="info-value">
                        <span>{{ $coupon->updated_at->format('Y年m月d日 H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 適用キャストセクション -->
        <div class="coupon-items-section">
            <h3 class="section-title">
                <i class="fas fa-users"></i>適用キャスト
            </h3>
            @if($coupon->items->count() > 0)
                <div class="items-grid">
                    @foreach($coupon->items as $item)
                    <div class="item-card">
                        <a href="{{ route('admin.items.edit', $item) }}" class="item-link">
                            <i class="fas fa-user-circle"></i>
                            <span>{{ $item->item_name }}</span>
                            <i class="fas fa-external-link-alt link-icon"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>適用キャストが設定されていません</p>
                </div>
            @endif
        </div>

        <!-- アクションボタンセクション -->
        <div class="action-buttons-section">
            <div class="action-buttons">
                <a href="{{ route('admin.coupons.edit', $coupon) }}" class="admin-btn admin-btn-secondary">
                    <i class="fas fa-edit"></i>編集
                </a>
                <form action="{{ route('admin.coupons.toggle-status', $coupon) }}" method="POST" class="inline-form">
                    @csrf
                    <button type="submit" class="admin-btn admin-btn-warning">
                        <i class="fas fa-toggle-{{ $coupon->is_active ? 'off' : 'on' }}"></i>
                        {{ $coupon->is_active ? '無効化' : '有効化' }}
                    </button>
                </form>
                <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" class="inline-form" onsubmit="return confirm('本当に削除しますか？この操作は取り消せません。')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="admin-btn admin-btn-danger">
                        <i class="fas fa-trash"></i>削除
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.coupon-show-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 1.5rem;
}

.back-button-area {
    margin-bottom: 1.5rem;
}

/* メインカード */
.coupon-detail-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

/* ヘッダーセクション */
.coupon-header-section {
    background:  #667eea ;
    padding: 2rem;
    color: white;
}

.coupon-title-area {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.coupon-title {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 600;
    color: white;
    flex: 1;
}

.coupon-status-badge .status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.coupon-status-badge .status-active {
    background: rgba(34, 197, 94, 0.3);
    border-color: rgba(34, 197, 94, 0.5);
}

.coupon-status-badge .status-inactive {
    background: rgba(239, 68, 68, 0.3);
    border-color: rgba(239, 68, 68, 0.5);
}

/* 画像セクション */
.coupon-image-section {
    padding: 2rem;
    background: #f8f9fa;
    text-align: center;
}

.image-wrapper {
    display: inline-block;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    background: white;
    padding: 1rem;
}

.coupon-main-image {
    max-width: 100%;
    max-height: 400px;
    width: auto;
    height: auto;
    border-radius: 8px;
    object-fit: contain;
}

/* 情報セクション */
.coupon-info-section,
.coupon-items-section {
    padding: 2rem;
    border-bottom: 1px solid #e5e7eb;
}

.coupon-items-section:last-of-type {
    border-bottom: none;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.25rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0 0 1.5rem 0;
    padding-bottom: 0.75rem;
    border-bottom: 2px solid #e5e7eb;
}

.section-title i {
    color: #667eea;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.info-item.full-width {
    grid-column: 1 / -1;
}

.info-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #4b5563;
    font-size: 0.875rem;
}

.info-label i {
    color: #667eea;
    width: 16px;
}

.info-value {
    color: #1f2937;
    font-size: 1rem;
}

.code-badge {
    display: inline-block;
    background: #f3f4f6;
    border: 2px dashed #667eea;
    color: #667eea;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-weight: 600;
    font-family: 'Courier New', monospace;
    letter-spacing: 1px;
}

.description-text {
    margin: 0;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 8px;
    border-left: 4px solid #667eea;
    white-space: pre-wrap;
    word-wrap: break-word;
    line-height: 1.6;
}

.text-muted {
    color: #9ca3af;
    font-style: italic;
}

/* 適用キャストセクション */
.items-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
}

.item-card {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.item-card:hover {
    background: #f3f4f6;
    border-color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.2);
}

.item-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem;
    color: #1f2937;
    text-decoration: none;
    font-weight: 500;
}

.item-link i:first-child {
    color: #667eea;
    font-size: 1.25rem;
}

.item-link span {
    flex: 1;
}

.link-icon {
    color: #9ca3af;
    font-size: 0.875rem;
    transition: color 0.2s;
}

.item-card:hover .link-icon {
    color: #667eea;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #9ca3af;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state p {
    margin: 0;
    font-size: 1rem;
}

/* アクションボタンセクション */
.action-buttons-section {
    padding: 2rem;
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
}

.action-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    justify-content: flex-start;
}

.action-buttons a,

.action-buttons form
{
    display:block;
    
}


.inline-form {
    display: inline;
    margin: 0;
}

/* レスポンシブ対応 */
@media (max-width: 768px) {
    .coupon-show-container {
        padding: 1rem;
    }

    .coupon-header-section {
        padding: 1.5rem;
    }

    .coupon-title {
        font-size: 1.5rem;
    }

    .coupon-title-area {
        flex-direction: column;
        align-items: flex-start;
    }

    .coupon-info-section,
    .coupon-items-section,
    .action-buttons-section {
        padding: 1.5rem;
    }

    .info-grid {
        grid-template-columns: 1fr;
    }

    .items-grid {
        grid-template-columns: 1fr;
    }

    .action-buttons {
        flex-direction: column;
    }

    .action-buttons .admin-btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

</x-admin-layout>

