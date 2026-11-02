<x-admin-layout>
    <x-slot name="header">
        問い合わせ管理
    </x-slot>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- 検索フォーム（メッセージ管理と同様のスタイル） --}}
    <form method="GET" action="{{ route('admin.contacts.index') }}" class="af-search">
        <div class="af-search-row">
            <div class="af-search-field" style="min-width:120px; max-width:140px;">
                <label for="contact_status">ステータス</label>
                <select name="status" id="contact_status" class="af-select">
                    <option value="">すべて</option>
                    @foreach(App\Models\Contact::getStatuses() as $key => $label)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="af-search-field" style="min-width:130px; max-width:170px;">
                <label for="contact_inquiry_type">問い合わせ種別</label>
                <select name="inquiry_type" id="contact_inquiry_type" class="af-select">
                    <option value="">すべて</option>
                    @foreach(App\Models\Contact::getInquiryTypes() as $key => $label)
                        <option value="{{ $key }}" {{ request('inquiry_type') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="af-search-field" style="min-width:200px;">
                <label for="contact_search">キーワード</label>
                <input type="text" name="search" id="contact_search" class="af-input" value="{{ request('search') }}"
                       placeholder="名前、件名、内容で検索">
            </div>
        </div>
        <div class="af-btn-row af-btn-row-center" style="margin-top:0.5rem;">
            <button type="submit" class="af-btn af-btn-search">
                <i class="fas fa-search"></i> 検索
            </button>
            <a href="{{ route('admin.contacts.index') }}" class="af-btn af-btn-reset">
                <i class="fas fa-redo"></i> リセット
            </a>
        </div>
    </form>

    <!-- 問い合わせ一覧 -->
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>受信日時</th>
                    <th>お名前</th>
                    <th>件名</th>
                    <th>種別</th>
                    <th>ステータス</th>
                    <th>スパム</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach($contacts as $contact)
                    <tr class="{{ $contact->status == 'unread' ? 'unread-row' : '' }}">
                        <td>{{ $contact->id }}</td>
                        <td>{{ $contact->created_at->format('Y/m/d H:i') }}</td>
                        <td>{{ $contact->name }}</td>
                        <td>
                            <div class="subject-cell">
                                {{ Str::limit($contact->subject, 30) }}
                            </div>
                        </td>
                        <td>{{ $contact->inquiry_type_label }}</td>
                        <td>
                            <span class="status-badge status-{{ $contact->status }}">
                                {{ $contact->status_label }}
                            </span>
                        </td>
                        <td>
                            @if($contact->is_spam)
                                <span class="badge badge-danger">スパム</span>
                            @else
                                <span class="badge badge-success">正常</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.contacts.show', $contact) }}"
                               class="btn btn-sm btn-primary">詳細</a>
                            <form method="POST" action="{{ route('admin.contacts.destroy', $contact) }}"
                                  style="display: inline;" onsubmit="return confirm('削除しますか？')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">削除</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- ページネーション -->
    <div class="pagination-wrapper">
        {{ $contacts->appends(request()->query())->links() }}
    </div>

    <style>
    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        font-size: 14px;
        margin-right: 5px;
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn-sm {
        padding: 4px 8px;
        font-size: 12px;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .table th,
    .table td {
        padding: 12px;
        text-align: left;
        border-bottom: 1px solid #dee2e6;
    }

    .table th {
        background-color: #f8f9fa;
        font-weight: bold;
    }

    .unread-row {
        background-color: #fff3cd;
    }

    .subject-cell {
        max-width: 200px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .status-badge {
        padding: 4px 8px;
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
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: bold;
    }

    .badge-danger {
        background-color: #dc3545;
        color: white;
    }

    .badge-success {
        background-color: #28a745;
        color: white;
    }

    .pagination-wrapper {
        text-align: center;
        margin-top: 20px;
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
