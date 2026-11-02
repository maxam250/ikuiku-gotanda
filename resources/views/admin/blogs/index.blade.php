<x-admin-layout>

<x-slot name="header">ジャーナル管理</x-slot>

<div class="af-page">
    <div class="af-page-header">
        <h1 class="af-page-title">ジャーナル一覧</h1>
        <a href="{{ route('admin.blogs.create') }}" class="af-btn af-btn-primary">
            <i class="fas fa-plus"></i> 新規作成
        </a>
    </div>

    @if (session('success'))
        <div class="af-alert af-alert-success">{{ session('success') }}</div>
    @endif

    <div class="af-table-wrap">
        <table class="af-table">
            <thead>
                <tr>
                    <th style="width:100px;">メイン画像</th>
                    <th>タイトル</th>
                    <th style="width:80px;">公開</th>
                    <th style="width:130px;">作成日</th>
                    <th style="width:120px;">操作</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($blogs as $blog)
                    <tr>
                        <td>
                            @if ($blog->main_image)
                                <img src="{{ Storage::url($blog->main_image) }}" alt=""
                                     style="width:80px; height:55px; object-fit:cover; border-radius:4px; border:1px solid #e5e7eb; display:block;">
                            @else
                                <span style="color:#ccc; font-size:0.78rem;">なし</span>
                            @endif
                        </td>
                        <td>{{ $blog->title }}</td>
                        <td>
                            <span class="af-badge {{ $blog->is_published ? 'af-badge-on' : 'af-badge-off' }}">
                                {{ $blog->is_published ? '公開' : '非公開' }}
                            </span>
                        </td>
                        <td style="font-size:0.78rem; color:#6b7280;">{{ $blog->created_at->format('Y/m/d H:i') }}</td>
                        <td>
                            <div class="af-btn-row">
                                <a href="{{ route('admin.blogs.edit', $blog) }}" class="af-btn af-btn-edit af-btn-sm">
                                    <i class="fas fa-edit"></i> 編集
                                </a>
                                <form action="{{ route('admin.blogs.destroy', $blog) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="af-btn af-btn-delete af-btn-sm"
                                            onclick="return confirm('本当に削除しますか？')">
                                        <i class="fas fa-trash"></i> 削除
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="margin-top:0.875rem;">
        {{ $blogs->links() }}
    </div>
</div>

</x-admin-layout>
