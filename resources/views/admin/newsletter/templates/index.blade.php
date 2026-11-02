<x-admin-layout>
    <x-slot name="header">
        メールマガジンテンプレート管理
    </x-slot>

    <x-newsletter-menu />

    @if(session('success'))
        <div class="alert alert-success" style="padding: 1rem; margin-bottom: 2rem; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="padding: 1rem; margin-bottom: 2rem; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px;">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white shadow-lg rounded-lg">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-800">テンプレート一覧</h2>
                <a href="{{ route('admin.newsletter.templates.create') }}" class="af-btn af-btn-primary">
                    新規作成
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">テンプレート名</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">件名</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">作成日</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($templates as $template)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $template->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $template->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ \Illuminate\Support\Str::limit($template->subject, 50) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $template->created_at->format('Y/m/d H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.newsletter.templates.preview', $template->id) }}" target="_blank" class="af-btn af-btn-cancel af-btn-sm">プレビュー</a>
                                <a href="{{ route('admin.newsletter.templates.edit', $template->id) }}" class="af-btn af-btn-edit af-btn-sm">編集</a>
                                <form action="{{ route('admin.newsletter.templates.duplicate', $template->id) }}" method="POST" class="inline mr-3">
                                    @csrf
                                    <button type="submit" class="af-btn af-btn-primary af-btn-sm" onclick="return confirm('このテンプレートを複製しますか？')">複製</button>
                                </form>
                                <form action="{{ route('admin.newsletter.templates.destroy', $template->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="af-btn af-btn-delete af-btn-sm" onclick="return confirm('本当に削除しますか？')">削除</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $templates->links() }}
            </div>
        </div>
    </div>
</x-admin-layout>

