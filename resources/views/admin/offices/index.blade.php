<x-admin-layout>
    <x-slot name="header">事務所管理</x-slot>

    <div class="w-full py-8 px-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">事務所管理</h1>
            <a href="{{ route('admin.offices.create') }}"
               class="admin-btn admin-btn-primary">
                <i class="fas fa-plus mr-1"></i> 新規追加
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                @if($offices->isEmpty())
                    <p class="text-gray-500 text-center py-8">事務所が登録されていません</p>
                @else
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">名前</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">メモ</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">並び順</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">ステータス</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">操作</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($offices as $office)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900">#{{ $office->id }}</td>
                                    <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $office->name }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ Str::limit($office->memo, 40) }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-900">{{ $office->sort }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $office->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $office->is_active ? '有効' : '無効' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.offices.edit', $office) }}"
                                               class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1 rounded-md">
                                                編集
                                            </a>
                                            <form action="{{ route('admin.offices.destroy', $office) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        onclick="return confirm('削除しますか？')"
                                                        class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md">
                                                    削除
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
