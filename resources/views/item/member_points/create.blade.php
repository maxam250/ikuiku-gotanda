
<x-item-layout>


<x-slot name="header">
会員新規登録
</x-slot>


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

<form action="{{ route('item.members.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="form-group form_parts">
        <select name="flag" class="flag-select">
            <option value="0" {{ old('flag') == 0 ? 'selected' : '' }}>未設定</option>
            <option value="1" {{ old('flag') == 1 ? 'selected' : '' }}>確認中</option>
            <option value="2" {{ old('flag') == 2 ? 'selected' : '' }}>確定</option>
            <option value="3" {{ old('flag') == 3 ? 'selected' : '' }}>保留</option>
        </select>
    </div>

    <div class="form-group form_parts">
        <input type="radio" name="kind" id="kind1" class="form-control" value="1" {{ old('kind' ?? '') == '1' || empty(old('kind' ?? '')) ? 'checked' : '' }}>
    <label for="kind1" class="radio">通常予約</label>

    <input type="radio" name="kind" id="kind2" class="form-control" value="2" {{ old('kind' ?? '') == '2' ? 'checked' : '' }}>
    <label for="kind2" class="radio">リクエスト予約</label>
    </div>
    <div class="form-group">
        <label for="date">日時</label>
        <input type="date" name="date" id="date" class="form-control size_s" value="{{ old('date' ?? '') }}">&nbsp;
        <select name="time_start">
            @foreach (generateTimeOptions('09:00', '23:00') as $value => $label)
            <option value="{{ $value }}" {{ old('time_start' ?? '') == $value ? 'selected' : '' }}>{{ $label }}　</option>
            @endforeach
        </select><div class="unit">～</div>
    </div>


    <div class="form-group">
        <label for="course_id">コース</label>
        <select name="course_id" id="course_id" class="form-control size_s">
            <option value="" disabled selected>選択してください</option>
            @foreach($courses as $course)
                <option value="{{ $course->id }}"{{ old('course_id' ?? '') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="course_time">時間</label>
        <select name="course_time" id="course_time" class="form-control size_s">
            <option value="" disabled selected>選択してください</option>
        </select>
    </div>

    <div class="form-group">
        <label for="item_id">キャスト</label>
        <select name="item_id" id="item_id" class="form-control size_s">
            <option value="0" selected>指名なし</option>
            @foreach($items as $item)
                <option value="{{ $item->id }}"{{ old('item_id' ?? '') == $item->id ? 'selected' : '' }}>{{ $item->item_name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="item_name">キャストその他</label>
        <input type="text" name="item_name" id="item_name" class="form-control size_s" value="{{ old('item_name' ?? '') }}">
    </div>

    <div class="form-group">
        <label for="option">オプション</label>
        <div class="flex form_parts">
        @php
            $oldOption = old('option' ?? '');
            $optionVals = is_array($oldOption) ? $oldOption : explode('-', $oldOption);
        @endphp
        @foreach($options as $option)
        <input type="checkbox"
           name="option[]"
           id="option{{ $option->id }}"
           value="{{ $option->id }}"
           {{ in_array($option->id, $optionVals) ? 'checked' : '' }}>
    <label for="option{{ $option->id }}" class="checkbox">{{ $option->title }}</label>
        @endforeach
        </div>
    </div>

    <div class="form-group">
        <label for="name">名前</label>
        <input type="text" name="name" id="name" class="form-control"  value="{{ old('name' ?? '') }}" required>
        &emsp;
        <select name="member_id">
            <option value="0">非会員</option>
        @foreach($members as $member)
            <option value="{{ $member->id }}"{{ old('member_id') == $member->id ? 'selected' : '' }}>[{{ $member->id }}] {{ $member->name }}</option>
        @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="email">メールアドレス</label>
        <input type="text" name="email" id="email" class="form-control" value="{{ old('email' ?? '') }}" required>
    </div>

    <div class="form-group">
        <label for="tel">電話番号</label>
        @php
            $oldTel = old('tel' ?? '');
            $telParts = is_array($oldTel) ? $oldTel : explode('-', $oldTel);
        @endphp
        <input type="text" name="tel[]" id="tel" class="size_s" value="{{ $telParts[0] ?? '' }}"  placeholder="090"><div class="unit">-&emsp;</div>
        <input type="text" name="tel[]" id="tel2" class="size_s" value="{{ $telParts[1] ?? '' }}"  placeholder="0000"><div class="unit">-&emsp;</div>
        <input type="text" name="tel[]" id="tel3" class="size_s" value="{{ $telParts[2] ?? '' }}"  placeholder="0000">
    </div>

    <div class="form-group">
        <label for="area">ご利用エリア</label>
        <input type="text" name="area" id="area" class="form-control size_m" value="{{ old('area') }}" >
    </div>

    <div class="form-group">
        <label for="place">具体的な場所</label>
        <input type="text" name="place" id="place" class="form-control size_m" value="{{ old('place') }}" >
    </div>

    <div class="form-group">
        <label for="meet">待ち合わせ方法</label>
        <input type="text" name="meet" id="meet" class="form-control size_m" value="{{ old('meet') }}" >
    </div>

    <div class="form-group">
        <label for="address">住所</label>
        <input type="text" name="address" id="address" class="form-control size_m" value="{{ old('address') }}" >
    </div>

    <div class="form-group">
        <label for="comment">備考</label>
        <textarea name="comment" id="comment" class="form-control">{{ old('comment') }}</textarea>
    </div>
    <div class="form-group">
        <label for="memo">メモ</label>
        <textarea name="memo" id="memo" class="form-control">{{ old('memo') }}</textarea>
    </div>
    <div class="form-group">
        <label for="memo_share">メモ（共有用）</label>
        <textarea name="memo_share" id="memo_share" class="form-control">{{ old('memo_share') }}</textarea>
    </div>
    <div class="form-group">
        <label for="memo_result">予約完了時のメモ</label>
        <textarea name="memo_result" id="memo_result" class="form-control">{{ old('memo_result') }}</textarea>
    </div>


    <div class="form-group">
        <label for="fee_course">コース料金</label>
        <input type="text" name="fee_course" id="fee_course" class="size_s" value="{{ old('fee_course') }}" >
        <div class="unit">円</div>
    </div>
    <div class="form-group">
        <label for="fee_option">オプション料金</label>
        <input type="text" name="fee_option" id="fee_option" class="size_s" value="{{ old('fee_option') }}" >
        <div class="unit">円</div>
    </div>
    <div class="form-group">
        <label for="fee_adjust">調整金</label>
        <input type="text" name="fee_adjust" id="fee_adjust" class="size_s" value="{{ old('fee_adjust') }}" >
        <div class="unit">円</div>
    </div>
    <div class="form-group">
        <label for="fee_adjust_memo">調整金メモ</label>
        <textarea name="fee_adjust_memo" id="fee_adjust_memo" class="form-control">{{ old('fee_adjust_memo') }}</textarea>
    </div>


    <button type="submit" class="btn btn-primary"> 更新する </button>
</form>

</section>


<script>
document.addEventListener('DOMContentLoaded', function() {
    var courseId = "{{ old('course_id') }}";
    var courseFeeSelect = document.getElementById('course_time');
    var oldCourseTime = "{{ old('course_time') }}";

    function loadCourseFees(courseId) {
        if (courseId) {
            fetch(`/get-course-fees/${courseId}`)
                .then(response => response.json())
                .then(data => {
                    courseFeeSelect.innerHTML = '<option value="" disabled selected>選択してください</option>';
                    data.forEach(courseFee => {
                        var option = document.createElement('option');
                        option.value = courseFee.id;
                        option.textContent = courseFee.course;
                        if (courseFee.id == oldCourseTime) {
                            option.selected = true;
                        }
                        courseFeeSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error:', error));
        }
    }

    // 初期ロード時にコース時間をロード
    loadCourseFees(courseId);

    // コース変更時のイベントリスナー
    document.getElementById('course_id').addEventListener('change', function() {
        loadCourseFees(this.value);
    });
});
</script>


</x-item-layout>
