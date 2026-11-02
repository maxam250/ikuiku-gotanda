<x-admin-layout>
    <x-slot name="header">
        会員編集
    </x-slot>

    <!-- 関連ページへのナビゲーション -->
    <div class="mb-6 flex flex-wrap gap-3">
        <a href="{{ route('admin.member_points.edit', ['member'=> $member->id]) }}"
           class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
            ポイント追加/削除
        </a>
        <a href="{{ route('admin.member_histories.index', ['member'=> $member->id]) }}"
           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            利用履歴一覧
        </a>
        <a href="{{ route('admin.member_histories.create', ['member'=> $member->id]) }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            利用履歴追加
        </a>
    </div>

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

    @if (session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white shadow-sm rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">会員情報編集</h3>
        </div>

        <form action="{{ route('admin.members.update', $member->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- 会員種別 -->
                <div>
                    <label for="kind" class="block text-sm font-medium text-gray-700 mb-2">会員種別</label>
                    <select name="kind" id="kind" data-member-id="{{ $member->id }}" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="0" {{ old('kind',$member['kind']) == 0 ? 'selected' : '' }}>停止</option>
                        <option value="1" {{ old('kind',$member['kind']) == 1 ? 'selected' : '' }}>通常</option>
                        <option value="2" {{ old('kind',$member['kind']) == 2 ? 'selected' : '' }}>プレミア</option>
                    </select>
                </div>

                <!-- 会員番号 -->
                <div>
                    <label for="member_no" class="block text-sm font-medium text-gray-700 mb-2">会員番号</label>
                    <input type="text" name="member_no" id="member_no"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('member_no', $member['member_no'] ?? '') }}">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- 名前 -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">名前 <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" required
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('name', $member['name'] ?? '') }}">
                </div>

                <!-- ハンドルネーム -->
                <div>
                    <label for="nickname" class="block text-sm font-medium text-gray-700 mb-2">ハンドルネーム</label>
                    <input type="text" name="nickname" id="nickname"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('nickname', $member['nickname'] ?? '') }}">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- ログインID -->
                <div>
                    <label for="loginid" class="block text-sm font-medium text-gray-700 mb-2">ログインID</label>
                    <input type="text" name="loginid" id="loginid"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('loginid', $member['loginid'] ?? '') }}">
                </div>

                <!-- パスワード -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">パスワード</label>
                    <input type="password" name="password" id="password" placeholder="変更する場合のみ入力"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- メールアドレス -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">メールアドレス <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="email" required
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                       value="{{ old('email', $decryptedEmail ?? '') }}">
            </div>

            <!-- 電話番号 -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">電話番号</label>
                @php
                    $oldTel = old('tel', $member['tel'] ?? '');
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
                        <option value="0"{{ old('gender', $member['gender'] ?? '') == 0 ? ' selected' : '' }}>未設定</option>
                        <option value="1"{{ old('gender', $member['gender'] ?? '') == 1 ? ' selected' : '' }}>男性</option>
                        <option value="2"{{ old('gender', $member['gender'] ?? '') == 2 ? ' selected' : '' }}>女性</option>
                        <option value="3"{{ old('gender', $member['gender'] ?? '') == 3 ? ' selected' : '' }}>その他</option>
                    </select>
                </div>

                <!-- LINE -->
                <div>
                    <label for="line" class="block text-sm font-medium text-gray-700 mb-2">LINE</label>
                    <input type="text" name="line" id="line"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('line', $member['line'] ?? '') }}">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- SNS -->
                <div>
                    <label for="sns" class="block text-sm font-medium text-gray-700 mb-2">SNS</label>
                    <input type="text" name="sns" id="sns"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('sns', $member['sns'] ?? '') }}">
                </div>

                <!-- Web -->
                <div>
                    <label for="url" class="block text-sm font-medium text-gray-700 mb-2">Web</label>
                    <input type="url" name="url" id="url"
                           class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                           value="{{ old('url', $member['url'] ?? '') }}">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- メモ -->
                <div>
                    <label for="memo" class="block text-sm font-medium text-gray-700 mb-2">メモ</label>
                    <textarea name="memo" id="memo" rows="4"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('memo', $member['memo'] ?? '') }}</textarea>
                </div>

                <!-- メモ（共有用） -->
                <div>
                    <label for="memo_share" class="block text-sm font-medium text-gray-700 mb-2">メモ（共有用）</label>
                    <textarea name="memo_share" id="memo_share" rows="4"
                              class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">{{ old('memo_share', $member['memo_share'] ?? '') }}</textarea>
                </div>
            </div>

            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <a href="{{ route('admin.member_histories.index', ['member' => $member->id]) }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition duration-150 ease-in-out">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    利用履歴を表示
                </a>

                <button type="submit"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-sm font-medium rounded-md transition duration-150 ease-in-out">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    更新する
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>
