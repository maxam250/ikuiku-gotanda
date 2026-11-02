<x-admin-layout>
    <x-slot name="header">
        問い合わせ詳細
    </x-slot>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    <div class="back">
        <a href="{{ route('admin.contacts.index') }}" class="btn-primary">一覧に戻る</a>
    </div>
    <div class="contact-detail">
        <div class="detail-header">
            <h2>問い合わせ #{{ $contact->id }}</h2>
            <div class="status-info">
                <span class="status-badge status-{{ $contact->status }}">
                    {{ $contact->status_label }}
                </span>
                @if($contact->is_spam)
                    <span class="badge badge-danger">スパム</span>
                @endif
            </div>
        </div>

        <div class="detail-content">
            <div class="detail-section">
                <h3>基本情報</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>受信日時:</label>
                        <span>{{ $contact->created_at->format('Y年m月d日 H:i:s') }}</span>
                    </div>
                    <div class="info-item">
                        <label>お名前:</label>
                        <span>{{ $contact->name }}</span>
                    </div>
                    <div class="info-item">
                        <label>メールアドレス:</label>
                        <span>{{ $contact->email }}</span>
                    </div>
                    @if(!empty($contact->phone))
                    <div class="info-item">
                        <label>電話番号:</label>
                        <span>{{ $contact->phone }}</span>
                    </div>
                    @endif
                    <div class="info-item">
                        <label>問い合わせ種別:</label>
                        <span>{{ $contact->inquiry_type_label }}</span>
                    </div>
                    <div class="info-item">
                        <label>件名:</label>
                        <span>{{ $contact->subject }}</span>
                    </div>
                </div>
            </div>

            <div class="detail-section">
                <h3>お問い合わせ内容</h3>
                <div class="message-content">{{ e($contact['message']) }}</div>
            </div>

            @if($contact->is_spam)
            <div class="detail-section">
                <h3>スパム情報</h3>
                <div class="spam-info">
                    <p><strong>IPアドレス:</strong> {{ $contact->ip_address }}</p>
                    <p><strong>ユーザーエージェント:</strong> {{ $contact->user_agent }}</p>
                </div>
            </div>
            @endif

            <div class="detail-section">
                <h3>管理者メモ</h3>
                <form method="POST" action="{{ route('admin.contacts.update', $contact) }}" class="memo-form">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label for="status">ステータス:</label>
                        <select name="status" id="status" class="form-control">
                            @foreach(App\Models\Contact::getStatuses() as $key => $label)
                                <option value="{{ $key }}" {{ $contact->status == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="admin_memo">メモ:</label>
                        <textarea name="admin_memo" id="admin_memo" rows="5"
                                  class="form-control" placeholder="管理者用メモを入力してください">{{ $contact->admin_memo }}</textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">更新</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
    .back-link {
        margin-bottom: 20px;
    }

    .back-link .btn {
        display: inline-block;
        padding: 10px 20px;
        background-color: #6c757d;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        font-size: 14px;
    }

    .back-link .btn:hover {
        background-color: #545b62;
    }

    .contact-detail {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .detail-header {
        background: #f8f9fa;
        padding: 20px;
        border-bottom: 1px solid #57add0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .detail-header h2 {
        margin: 0;
        color: #333;
    }

    .status-info {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .detail-content {
        padding: 20px;
    }

    .detail-section {
        margin-bottom: 30px;
    }

    .detail-section h3 {
        color: #333;
        border-bottom: 2px solid #57add0;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 15px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-item label {
        font-weight: bold;
        color: #495057;
        margin-bottom: 5px;
    }

    .info-item span {
        color: #333;
        padding: 8px 12px;
        background: #f8f9fa;
        border-radius: 4px;
        border-left: 4px solid #57add0;
    }

    .message-content {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 4px;
        border: 1px solid #57add0;
        white-space: pre-wrap;
        line-height: 1.6;
        min-height: 150px;
    }

    .spam-info {
        background: #fff3cd;
        padding: 15px;
        border-radius: 4px;
        border: 1px solid #ffeaa7;
    }

    .spam-info p {
        margin: 5px 0;
    }

    .memo-form {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 4px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
        color: #495057;
    }

    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }

    .form-control:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
    }

    .form-actions {
        text-align: right;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        font-size: 14px;
        margin-right: 10px;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #545b62;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: bold;
    }

    .status-unread {
        background-color: #dc3545;
        color: white;
    }

    .status-in_progress {
        background-color: #ffc107;
        color: #212529;
    }

    .status-completed {
        background-color: #28a745;
        color: white;
    }

    .badge {
        padding: 6px 12px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: bold;
    }

    .badge-danger {
        background-color: #dc3545;
        color: white;
    }

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }

    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    }
    </style>
</x-admin-layout>
