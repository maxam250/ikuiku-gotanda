<x-admin-layout>
    <x-slot name="header">
        <h2>禁止ワード管理</h2>
    </x-slot>

    <div class="py-6 px-4 max-w-3xl mx-auto">

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        <div class="mb-4">
            <a href="{{ route('admin.fanclub-messages.index') }}" class="px-3 py-1.5 border rounded text-sm">&larr; メッセージ監視へ</a>
        </div>

        <p class="text-sm text-gray-600 mb-4">
            登録した単語が含まれるメッセージは送信をブロックします。
        </p>

        {{-- 追加フォーム --}}
        <form method="POST" action="{{ route('admin.fanclub-banned-words.store') }}"
              class="flex gap-3 mb-6">
            @csrf
            <input type="text" name="word" required maxlength="100"
                   placeholder="禁止ワードを入力"
                   class="border rounded px-3 py-1.5 text-sm flex-1">
            @error('word')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
            <button type="submit"
                    class="px-4 py-1.5 bg-blue-600 text-white rounded text-sm">追加</button>
        </form>

        {{-- 一覧 --}}
        <table class="w-full border-collapse text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-3 py-2 text-left">ワード</th>
                    <th class="border px-3 py-2 text-left">登録日</th>
                    <th class="border px-3 py-2"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($words as $word)
                <tr>
                    <td class="border px-3 py-2">{{ $word->word }}</td>
                    <td class="border px-3 py-2 text-xs">{{ $word->created_at->format('Y/m/d') }}</td>
                    <td class="border px-3 py-2">
                        <form method="POST"
                              action="{{ route('admin.fanclub-banned-words.destroy', $word) }}"
                              onsubmit="return confirm('削除しますか？')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs">削除</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="border px-3 py-4 text-center text-gray-400">登録されていません</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">{{ $words->links() }}</div>
    </div>
</x-admin-layout>
