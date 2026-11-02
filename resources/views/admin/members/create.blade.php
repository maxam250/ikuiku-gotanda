
<x-admin-layout>
    <x-slot name="header">
        会員新規登録
    </x-slot>

    <div class="mb-6">
        <a href="{{ route('admin.members.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            戻る
        </a>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">入力エラーがあります</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">会員情報</h3>
        </div>

        <form action="{{ route('admin.members.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- 会員種別 -->
                <div>
                    <label for="kind" class="block text-sm font-medium text-gray-700 mb-2">会員種別</label>
                    <select name="kind" id="kind" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="0" {{ old('kind') == 0 ? 'selected' : '' }}>停止</option>
                        <option value="1" {{ old('kind') == 1 ? 'selected' : '' }}>通常</option>
                        <option value="2" {{ old('kind') == 2 ? 'selected' : '' }}>プレミア</option>
                    </select>
                </div>

                <!-- 会員番号 -->
                <div>
                    <label for="member_no" class="block text-sm font-medium text-gray-700 mb-2">会員番号</label>
                    <input type="text" name="member_no" id="member_no"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('member_no') }}">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- 名前 -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">名前 <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('name') }}">
                </div>

                <!-- ハンドルネーム -->
                <div>
                    <label for="nickname" class="block text-sm font-medium text-gray-700 mb-2">ハンドルネーム</label>
                    <input type="text" name="nickname" id="nickname"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('nickname') }}">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- ログインID -->
                <div>
                    <label for="loginid" class="block text-sm font-medium text-gray-700 mb-2">ログインID</label>
                    <input type="text" name="loginid" id="loginid"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- パスワード -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">パスワード</label>
                    <input type="password" name="password" id="password"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- メールアドレス -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">メールアドレス <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" required
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                       value="{{ old('email') }}">
            </div>

            <!-- 電話番号 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">電話番号</label>
                @php
                    $oldTel = old('tel');
                    $telParts = is_array($oldTel) ? $oldTel : explode('-', $oldTel);
                @endphp
                <div class="flex items-center space-x-2">
                    <input type="text" name="tel[]" placeholder="090"
                           class="block w-20 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ $telParts[0] ?? '' }}">
                    <span class="text-gray-500">-</span>
                    <input type="text" name="tel[]" placeholder="0000"
                           class="block w-20 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ $telParts[1] ?? '' }}">
                    <span class="text-gray-500">-</span>
                    <input type="text" name="tel[]" placeholder="0000"
                           class="block w-20 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ $telParts[2] ?? '' }}">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- 性別 -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">性別</label>
                    <select name="gender" id="gender" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="0"{{ old('gender') == 0 ? ' selected' : '' }}>未設定</option>
                        <option value="1"{{ old('gender') == 1 ? ' selected' : '' }}>男性</option>
                        <option value="2"{{ old('gender') == 2 ? ' selected' : '' }}>女性</option>
                        <option value="3"{{ old('gender') == 3 ? ' selected' : '' }}>その他</option>
                    </select>
                </div>

                <!-- LINE -->
                <div>
                    <label for="line" class="block text-sm font-medium text-gray-700 mb-2">LINE</label>
                    <input type="text" name="line" id="line"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('line') }}">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- SNS -->
                <div>
                    <label for="sns" class="block text-sm font-medium text-gray-700 mb-2">SNS</label>
                    <input type="text" name="sns" id="sns"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('sns') }}">
                </div>

                <!-- Web -->
                <div>
                    <label for="url" class="block text-sm font-medium text-gray-700 mb-2">Web</label>
                    <input type="url" name="url" id="url"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('url') }}">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- メモ -->
                <div>
                    <label for="memo" class="block text-sm font-medium text-gray-700 mb-2">メモ</label>
                    <textarea name="memo" id="memo" rows="4"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('memo') }}</textarea>
                </div>

                <!-- メモ（共有用） -->
                <div>
                    <label for="memo_share" class="block text-sm font-medium text-gray-700 mb-2">メモ（共有用）</label>
                    <textarea name="memo_share" id="memo_share" rows="4"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('memo_share') }}</textarea>
                </div>
            </div>

            <div class="flex justify-end pt-6 border-t border-gray-200">
                <button type="submit"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700  text-sm font-medium rounded-md transition duration-150 ease-in-out">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    登録する
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
