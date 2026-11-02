<x-item-layout>


    <x-slot name="header">
    会員編集
    </x-slot>


<section class="toPageArea">
    <a href="{{ route('item.member_points.edit', [ 'member_point'=> $member->id ] ) }}" class="point">ポイント追加/削除</a>
    <a href="{{ route('item.member_histories.create' ) }}" class="history">利用編集追加</a>
    <a href="{{ route('item.member_histories.edit', [ 'member_history'=> $member->id ] ) }}" class="history">利用履歴編集</a>
</section>


    <div class="back">
        <a href="{{ route('item.members.index') }}" class="btn-primary">戻る</a>
    </div>

    <section class="MessageArea">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
    </section>

    <section>

    <form action="{{ route('item.members.update', $member->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')


        <div class="form-group">
            <label for="kind">会員種別</label>
            <select name="kind" class="kind-select" data-member-id="{{ $member->id }}">
                <option value="0" {{ old('kind',$member['kind']) == 0 ? 'selected' : '' }}>停止</option>
                <option value="1" {{ old('kind',$member['kind']) == 1 ? 'selected' : '' }}>通常</option>
                <option value="2" {{ old('kind',$member['kind']) == 2 ? 'selected' : '' }}>プレミア</option>
            </select>
        </div>
        <div class="form-group">
            <label for="member_no">会員番号</label>
            <input type="text" name="member_no" id="member_no" class="form-control size_s"  value="{{ old('member_no', $member['member_no'] ?? '') }}">
            &emsp;
        </div>
        <div class="form-group">
            <label for="name">名前</label>
            <input type="text" name="name" id="name" class="form-control"  value="{{ old('name', $member['name'] ?? '') }}" required>
            &emsp;
        </div>
        <div class="form-group">
            <label for="nickname">ハンドルネーム</label>
            <input type="text" name="nickname" id="nickname" class="form-control"  value="{{ old('nickname', $member['nickname'] ?? '') }}">
            &emsp;
        </div>
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="text" name="email" id="email" class="form-control" value="{{ old('email', $decryptedEmail ?? '') }}" required>
        </div>

        <div class="form-group">
            <label for="tel">電話番号</label>
            @php
                $oldTel = old('tel', $member['tel'] ?? '');
                $telParts = is_array($oldTel) ? $oldTel : explode('-', $oldTel);
            @endphp
            <input type="text" name="tel[]" id="tel" class="size_s" value="{{ $telParts[0] ?? '' }}"  placeholder="090"><div class="unit">-&emsp;</div>
            <input type="text" name="tel[]" id="tel2" class="size_s" value="{{ $telParts[1] ?? '' }}"  placeholder="0000"><div class="unit">-&emsp;</div>
            <input type="text" name="tel[]" id="tel3" class="size_s" value="{{ $telParts[2] ?? '' }}"  placeholder="0000">
        </div>


        <div class="form-group">
            <label for="birth">生年月日</label>

            <!-- 年の選択 -->
            <select name="birth_year" class="size_s">
                <option value="" disabled {{ old('birth_year', $member['birth']->format('Y') ?? '') == '' ? 'selected' : '' }}>西暦</option>
                @for($y = $start_year; $y <= $end_year; $y++)
                    <option value="{{ $y }}" {{ old('birth_year', $member['birth']->format('Y') ?? '') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
            <div class="unit">年&emsp;</div>

            <!-- 月の選択 -->
            <select name="birth_month" class="size_s">
                <option value="" disabled {{ old('birth_month', $member['birth']->format('m') ?? '') == '' ? 'selected' : '' }}>月</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ old('birth_month', $member['birth']->format('m') ?? '') == $m ? 'selected' : '' }}>{{ $m }}</option>
                @endfor
            </select>
            <div class="unit">月&emsp;</div>

            <!-- 日の選択 -->
            <select name="birth_day" class="size_s">
                <option value="" disabled {{ old('birth_day', $member['birth']->format('d') ?? '') == '' ? 'selected' : '' }}>日</option>
                @for($d = 1; $d <= 31; $d++)
                    <option value="{{ $d }}" {{ old('birth_day', $member['birth']->format('d') ?? '') == $d ? 'selected' : '' }}>{{ $d }}</option>
                @endfor
            </select>
            <div class="unit">日&emsp;</div>
        </div>

        <div class="form-group">
            <label for="gender">性別</label>
            <select name="gender" id="gender" class="size_s">
                <option value="0"{{ old('gender', $member['gender'] ?? '') == 0 ? 'selected' : '' }} disable>未設定</option>
                <option value="1"{{ old('gender', $member['gender'] ?? '') == 1 ? 'selected' : '' }}>男性</option>
                <option value="2"{{ old('gender', $member['gender'] ?? '') == 2 ? 'selected' : '' }}>女性</option>
                <option value="3"{{ old('gender', $member['gender'] ?? '') == 3 ? 'selected' : '' }}>その他</option>
            </select>
        </div>

        <div class="form-group">
            <label for="line">LINE</label>
            <input type="text" name="line" id="line" class="form-control size_m" value="{{ old('line', $member['line'] ?? '') }}">
        </div>

        <div class="form-group">
            <label for="sns">SNS</label>
            <input type="text" name="sns" id="sns" class="form-control size_m" value="{{ old('sns', $member['sns'] ?? '') }}">
        </div>

        <div class="form-group">
            <label for="url">Web</label>
            <input type="text" name="url" id="url" class="form-control size_m" value="{{ old('url', $member['url'] ?? '') }}">
        </div>

        <div class="form-group">
            <label for="memo">メモ</label>
            <textarea name="memo" id="memo" class="form-control">{{ old('memo', $member['memo'] ?? '') }}</textarea>
        </div>
        <div class="form-group">
            <label for="memo_share">メモ（共有用）</label>
            <textarea name="memo_share" id="memo_share" class="form-control">{{ old('memo_share', $member['memo_share'] ?? '') }}</textarea>
        </div>

        <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" name="password" id="password" class="form-control" >
            &emsp;
        </div>

        <button type="submit" class="af-btn af-btn-primary">更新</button>
    </form>

</section>

</x-item-layout>
