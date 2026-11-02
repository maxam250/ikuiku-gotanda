<x-admin-layout>
<x-slot name="header">キャストメッセージ管理</x-slot>

<section>
<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>キャスト一覧</span>
                    <span id="unread-badge" class="badge bg-primary" style="display: none;"></span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($items as $item)
                            <a href="{{ route('admin.message_item.show', $item->id) }}"
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <!-- キャストのサムネイル画像 -->
                                    <div class="me-3">
                                        <img src="{{ $item->th_flename() }}" 
                                             alt="{{ $item->item_name }}" 
                                             class="rounded-circle" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $item->item_name }}</div>
                                        <small class="text-muted">
                                            @if($item->last_message_at)
                                                最終メッセージ: {{ \Carbon\Carbon::parse($item->last_message_at)->format('Y/m/d H:i') }}
                                            @endif
                                        </small>
                                    </div>
                                </div>
                                @if($item->unread_count > 0)
                                    <span class="badge bg-danger rounded-pill">{{ $item->unread_count }}</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</section>

<style>
.list-group-item {
    transition: background-color 0.2s ease;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.list-group-item.active {
    background-color: #007bff;
    border-color: #007bff;
}

.list-group-item.active .text-muted {
    color: rgba(255, 255, 255, 0.8) !important;
}

.rounded-circle {
    border: 2px solid #e9ecef;
}

.badge {
    font-size: 0.75rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const unreadBadge = document.getElementById('unread-badge');

    // 未読メッセージ数の更新
    function updateUnreadCount() {
        fetch('/kanrigamen/message_item/unread-count')
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    unreadBadge.textContent = data.count;
                    unreadBadge.style.display = 'inline';
                } else {
                    unreadBadge.style.display = 'none';
                }
            });
    }

    // 定期的な更新
    setInterval(updateUnreadCount, 30000); // 30秒ごとに未読数を更新

    // 初期表示時に未読数を更新
    updateUnreadCount();
});
</script>

</x-admin-layout> 