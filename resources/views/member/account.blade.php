<x-member-layout>
    <x-slot name="header">
    アカウント情報
    </x-slot>


    <section id="reservationArea">

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session()->has('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
    @endif


        <div class="inner">

            <section class="p-4">

            <form action="{{ route('member.accountUpdate', $member->id) }}" method="POST">
                @csrf
                @method('patch')
                <div class="member-account-field">
                    <label for="nickname">ハンドルネーム</label>
                    <input type="text" id="nickname" name="nickname" value="{{ $member->nickname }}" class="w-full">
                </div>
                <div class="member-account-field">
                    <label for="loginid">ログインID</label>
                    <input type="text" id="loginid" name="loginid" value="{{ $member->loginid }}" class="w-full" required>
                </div>
                <div class="member-account-field">
                    <label for="email">メールアドレス</label>
                    <input type="email" id="email" name="email" value="{{ $member->email }}" class="w-full">
                </div>
                <div class="member-account-field">
                    <label for="tel">電話番号</label>
                    @php
                        $oldTel = old('tel', $member['tel'] ?? '');
                        $telParts = is_array($oldTel) ? $oldTel : explode('-', $oldTel);
                    @endphp
                    <div class="unit">
                    <input type="text" name="tel[]" id="tel" class="size_s" value="{{ $telParts[0] ?? '' }}"  placeholder="090">&emsp;-&emsp;
                    <input type="text" name="tel[]" id="tel2" class="size_s" value="{{ $telParts[1] ?? '' }}"  placeholder="0000">&emsp;-&emsp;
                    <input type="text" name="tel[]" id="tel3" class="size_s" value="{{ $telParts[2] ?? '' }}"  placeholder="0000">
                    </div>
                </div>
                <div class="member-account-field">
                    <label for="memo_share">連絡事項</label>
                    <textarea id="memo_share" name="memo_share" class="size_30p">{{ $member->memo_share }}</textarea>
                </div>
                <div class="member-btn-group">
                    <button type="submit" class="member-btn member-btn-primary">
                        <i class="fas fa-save"></i>更新する
                    </button>
                </div>
            </form>


            </section>



        </div>


    </section>



</x-member-layout>
