<x-member-layout>
    <x-slot name="header">
    パスワード変更
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('status'))
                        <div class="mb-4 text-sm font-medium text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 text-sm font-medium text-red-600">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('member.account.passwordUpdate', ['id' => Auth::guard('member')->id()]) }}">
                        @csrf
                        @method('patch')

                        <div class="mt-4">
                            <x-input-label for="current_password" value="現在のパスワード" />
                            <x-text-input id="current_password" name="current_password" type="password" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="password" value="新しいパスワード" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <x-input-label for="password_confirmation" value="新しいパスワード（確認）" />
                            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <div class="member-btn-group">
                                <button type="submit" class="member-btn member-btn-primary">
                                    <i class="fas fa-key"></i>更新する
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-member-layout>
