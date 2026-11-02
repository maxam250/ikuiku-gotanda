@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>メッセージ一覧</span>
                    <span id="unread-badge" class="badge bg-primary" style="display: none;"></span>
                </div>

                <div class="card-body">
                    <div class="message-list" id="message-list">
                        @foreach($messages as $message)
                            <div class="message-item {{ $message->sender_id === Auth::id() ? 'sent' : 'received' }}" data-message-id="{{ $message->id }}">
                                <div class="message-content">
                                    <p>{{ $message->message }}</p>
                                    <small class="text-muted">
                                        {{ $message->created_at->format('Y/m/d H:i') }}
                                        @if(!$message->is_read && $message->receiver_id === Auth::id())
                                            <span class="badge bg-primary">未読</span>
                                        @endif
                                    </small>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{ $messages->links() }}

                    <form action="{{ route('messages.store') }}" method="POST" class="mt-4">
                        @csrf
                        <div class="form-group">
                            <label for="message">新しいメッセージ</label>
                            <textarea name="message" id="message" class="form-control" rows="3" required></textarea>
                        </div>
                        <input type="hidden" name="receiver_id" value="{{ $receiver_id }}">
                        <input type="hidden" name="receiver_type" value="{{ $receiver_type }}">
                        <button type="submit" class="btn btn-primary mt-2">送信</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.message-list {
    max-height: 500px;
    overflow-y: auto;
    padding: 1rem;
}

.message-item {
    margin-bottom: 1rem;
    display: flex;
    flex-direction: column;
}

.message-item.sent {
    align-items: flex-end;
}

.message-item.received {
    align-items: flex-start;
}

.message-content {
    max-width: 70%;
    padding: 0.75rem;
    border-radius: 1rem;
    background-color: #f8f9fa;
}

.message-item.sent .message-content {
    background-color: #007bff;
    color: white;
}

.message-item.received .message-content {
    background-color: #e9ecef;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageList = document.getElementById('message-list');
    const unreadBadge = document.getElementById('unread-badge');
    let lastMessageId = {{ $messages->first() ? $messages->first()->id : 0 }};

    // 未読メッセージの既読化
    function markMessagesAsRead() {
        const unreadMessages = document.querySelectorAll('.message-item.received .badge');
        unreadMessages.forEach(badge => {
            const messageItem = badge.closest('.message-item');
            const messageId = messageItem.dataset.messageId;
            
            fetch(`/messages/${messageId}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            });
        });
    }

    // 未読メッセージ数の更新
    function updateUnreadCount() {
        fetch('/messages/unread-count')
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

    // 新しいメッセージの取得
    function checkNewMessages() {
        fetch(`/messages?after=${lastMessageId}`)
            .then(response => response.json())
            .then(data => {
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(message => {
                        const messageHtml = createMessageHtml(message);
                        messageList.insertAdjacentHTML('afterbegin', messageHtml);
                        lastMessageId = Math.max(lastMessageId, message.id);
                    });
                    updateUnreadCount();
                }
            });
    }

    // メッセージHTMLの生成
    function createMessageHtml(message) {
        const isSent = message.sender_id === {{ Auth::id() }};
        const sentClass = isSent ? 'sent' : 'received';
        const unreadBadge = !isSent && !message.is_read ? 
            '<span class="badge bg-primary">未読</span>' : '';
        
        return `
            <div class="message-item ${sentClass}" data-message-id="${message.id}">
                <div class="message-content">
                    <p>${message.message}</p>
                    <small class="text-muted">
                        ${new Date(message.created_at).toLocaleString()}
                        ${unreadBadge}
                    </small>
                </div>
            </div>
        `;
    }

    // 定期的な更新
    setInterval(checkNewMessages, 30000); // 30秒ごとに更新
    setInterval(updateUnreadCount, 30000); // 30秒ごとに未読数を更新

    // 初期表示時に未読数を更新
    updateUnreadCount();

    // メッセージリストが表示されたら既読化
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                markMessagesAsRead();
            }
        });
    });

    observer.observe(messageList);
});
</script>
@endsection 