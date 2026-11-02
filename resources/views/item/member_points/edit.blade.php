<x-item-layout>


    <x-slot name="header">
    会員編集
    </x-slot>

    
<section class="toPageArea">
    <a href="{{ route('item.member_points.edit', [ 'member_point'=> $members->id ] ) }}" class="point">ポイント追加/削除</a>
    <a href="{{ route('item.member_histories.create', [ 'member_history'=> $members->id ] ) }}" class="history">利用編集追加</a>
    <a href="{{ route('item.member_histories.edit', [ 'member_history'=> $members->id ] ) }}" class="history">利用履歴編集</a>
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
    </section>

    <section>

    <form action="{{ route('item.members.update', $members->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')


        <div class="form-group">
            <label for="kind">会員種別</label>
            <select name="kind" class="kind-select" data-member-id="{{ $members->id }}">
                <option value="0" {{ old('kind',$members['kind']) == 0 ? 'selected' : '' }}>停止</option>
                <option value="1" {{ old('kind',$members['kind']) == 1 ? 'selected' : '' }}>通常</option>
                <option value="2" {{ old('kind',$members['kind']) == 2 ? 'selected' : '' }}>プレミア</option>
            </select>
        </div>
        <div class="form-group">
            <label for="name">名前</label>
            <input type="text" name="name" id="name" class="form-control"  value="{{ old('name', $members['name'] ?? '') }}" required>
            &emsp;
        </div>
        <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="text" name="email" id="email" class="form-control" value="{{ old('email', $members['email'] ?? '') }}" required>
        </div>

        <div class="form-group">
            <label for="tel">電話番号</label>
            @php
                $oldTel = old('tel', $members['tel'] ?? '');
                $telParts = is_array($oldTel) ? $oldTel : explode('-', $oldTel);
            @endphp
            <input type="text" name="tel[]" id="tel" class="size_ss" value="{{ $telParts[0] ?? '' }}"  placeholder="090"><div class="unit">-&emsp;</div>
            <input type="text" name="tel[]" id="tel2" class="size_ss" value="{{ $telParts[1] ?? '' }}"  placeholder="0000"><div class="unit">-&emsp;</div>
            <input type="text" name="tel[]" id="tel3" class="size_ss" value="{{ $telParts[2] ?? '' }}"  placeholder="0000">
        </div>


        <div class="form-group">
            <label for="birth">生年月日</label>
            @php
                $oldBirth = old('birth', $members['birth'] ?? '');
                $birthParts = is_array($oldBirth) ? $oldBirth : explode('-', $oldBirth);
            @endphp

            <select name="birth[]"  class="size_s">
                <option value="" disabled selected>西暦</option>
                @for($y=$start_year; $y <= $end_year; $y++)
                <option value="{{ $y }}"{{ $birthParts['0'] ? 'selected' : '' }}>{{ $y }}</option>
                @endfor            
            </select>
            <div class="unit">年&emsp;</div>

            <select name="birth[]"  class="size_s">
                <option value="" disabled selected>月</option>
                @for($m=1; $m <= 12; $m++)
                <option value="{{ $m }}"{{ $birthParts['1'] ? 'selected' : '' }}>{{ $m }}</option>
                @endfor            
            </select>
            <div class="unit">月&emsp;</div>

            <select name="birth[]"  class="size_s">
                <option value="" disabled selected>日</option>
                @for($d=1; $d < 31; $d++)
                <option value="{{ $d }}"{{ $birthParts['2'] ? 'selected' : '' }}>{{ $d }}</option>
                @endfor            
            </select>
            <div class="unit">日&emsp;</div>

        </div>
        
        <div class="form-group">
            <label for="gender">性別</label>
            <select name="gender" id="gender" class="size_s">
                <option value="0"{{ old('gender', $members['gender'] ?? '') == 0 ? 'selected' : '' }}>未設定</option>
                <option value="1"{{ old('gender', $members['gender'] ?? '') == 1 ? 'selected' : '' }}>男性</option>
                <option value="2"{{ old('gender', $members['gender'] ?? '') == 2 ? 'selected' : '' }}>女性</option>
                <option value="3"{{ old('gender', $members['gender'] ?? '') == 3 ? 'selected' : '' }}>その他</option>
            </select>
        </div>

        <div class="form-group">
            <label for="line">LINE</label>
            <input type="text" name="line" id="line" class="form-control size_m" value="{{ old('line', $members['line'] ?? '') }}">
        </div>

        <div class="form-group">
            <label for="sns">SNS</label>
            <input type="text" name="sns" id="sns" class="form-control size_m" value="{{ old('sns', $members['sns'] ?? '') }}">
        </div>

        <div class="form-group">
            <label for="url">Web</label>
            <input type="text" name="url" id="url" class="form-control size_m" value="{{ old('url', $members['url'] ?? '') }}">
        </div>

        <div class="form-group">
            <label for="memo">メモ</label>
            <textarea name="memo" id="memo" class="form-control">{{ old('memo', $members['memo'] ?? '') }}</textarea>
        </div>
        <div class="form-group">
            <label for="memo_share">メモ（共有用）</label>
            <textarea name="memo_share" id="memo_share" class="form-control">{{ old('memo_share', $members['memo_share'] ?? '') }}</textarea>
        </div>


        <button type="submit" class="btn btn-primary"> 更新する </button>
    </form>

</section>

</x-item-layout>
