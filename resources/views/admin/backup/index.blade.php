<x-admin-layout>
    <x-slot name="header">
        バックアップ管理
    </x-slot>

    <section class="content">
        <!-- メッセージ表示 -->
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <!-- オブジェクトストレージ（S3）バックアップ/リストア -->
        <div class="mb-6 p-6 bg-white rounded-xl shadow-sm border-l-4 border-orange-400">
            <h2 class="text-xl font-bold mb-4 text-gray-800">オブジェクトストレージ（S3）バックアップ / リストア</h2>
            <div class="flex gap-4">
                <form action="{{ route('admin.backup.s3Backup') }}" method="POST" class="inline"
                      onsubmit="return confirm('S3へのバックアップを実行します。\nよろしいですか？');">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors">
                        S3へ手動バックアップ
                    </button>
                </form>
                <form action="{{ route('admin.backup.s3Restore') }}" method="POST" class="inline"
                      onsubmit="return confirm('S3から最新バックアップを復元します。\n現在のファイルとデータベースは上書きされます。\n本当によろしいですか？');">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        S3から復元（リストア）
                    </button>
                </form>
            </div>
            <p class="mt-3 text-sm text-gray-500">
                ※ バックグラウンドで実行されます。進捗はサーバーの /var/log/backup.log または /var/log/restore.log で確認できます。
            </p>
        </div>

        <!-- 手動バックアップ実行 -->
        <div class="mb-6 p-6 bg-white rounded-xl shadow-sm">
            <h2 class="text-xl font-bold mb-4 text-gray-800">手動バックアップ実行（ローカル保存）</h2>
            <div class="flex gap-4">
                <form action="{{ route('admin.backup.create') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="type" value="images">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-500 rounded-lg hover:bg-blue-600 transition-colors">
                        画像バックアップ実行
                    </button>
                </form>
                <form action="{{ route('admin.backup.create') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="type" value="database">
                    <button type="submit"
                            class="px-4 py-2 bg-green-500 rounded-lg hover:bg-green-600 transition-colors">
                        データベースバックアップ実行
                    </button>
                </form>
                <form action="{{ route('admin.backup.create') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="type" value="all">
                    <button type="submit"
                            class="px-4 py-2 bg-purple-500 rounded-lg hover:bg-purple-600 transition-colors">
                        全バックアップ実行
                    </button>
                </form>
            </div>
            {{-- <p class="mt-4 text-sm text-gray-600">
                ※ 自動バックアップは毎日02:00に実行されます（7世代保持）
            </p> --}}
        </div>

        <!-- 画像バックアップ一覧 -->
        <div class="mb-6 p-6 bg-white rounded-xl shadow-sm">
            <h2 class="text-xl font-bold mb-4 text-gray-800">画像バックアップ一覧</h2>
            @if(count($imageBackups) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ファイル名</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">サイズ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">作成日時</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($imageBackups as $backup)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $backup['filename'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $backup['size_human'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $backup['created_at'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex gap-2">
                                            <a href="{{ route('admin.backup.download', ['type' => 'images', 'filename' => $backup['filename']]) }}"
                                               class="px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                                                ダウンロード
                                            </a>
                                            <form action="{{ route('admin.backup.destroy', ['type' => 'images', 'filename' => $backup['filename']]) }}"
                                                  method="POST"
                                                  class="inline"
                                                  onsubmit="return confirm('このバックアップファイルを削除してもよろしいですか？');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-3 py-1 bg-red-500 rounded hover:bg-red-600 transition-colors">
                                                    削除
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">画像バックアップファイルがありません。</p>
            @endif
        </div>

        <!-- データベースバックアップ一覧 -->
        <div class="mb-6 p-6 bg-white rounded-xl shadow-sm">
            <h2 class="text-xl font-bold mb-4 text-gray-800">データベースバックアップ一覧</h2>
            @if(count($databaseBackups) > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ファイル名</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">サイズ</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">作成日時</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">操作</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($databaseBackups as $backup)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $backup['filename'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $backup['size_human'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $backup['created_at'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex gap-2">
                                            <a href="{{ route('admin.backup.download', ['type' => 'database', 'filename' => $backup['filename']]) }}"
                                               class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 transition-colors">
                                                ダウンロード
                                            </a>
                                            <form action="{{ route('admin.backup.destroy', ['type' => 'database', 'filename' => $backup['filename']]) }}"
                                                  method="POST"
                                                  class="inline"
                                                  onsubmit="return confirm('このバックアップファイルを削除してもよろしいですか？');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-3 py-1 bg-red-500 rounded hover:bg-red-600 transition-colors">
                                                    削除
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-500">データベースバックアップファイルがありません。</p>
            @endif
        </div>
    </section>
</x-admin-layout>
