
<x-item-layout>


<x-slot name="header">
コース編集
</x-slot>

<div class="back">
    <a href="{{ route('item.course_fees.index') }}" class="btn-primary">戻る</a>
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

    <form action="{{ route('item.course_fees.store') }}" method="POST">
        @csrf
        <div class="form-group">

            <label for="course_id">カテゴリ</label>
            <select name="course_id" id="course_id" class="form-control" required>
                <option value="" disabled {{ old('course_id', $selectedCate ?? '') == '' ? 'selected' : '' }}>カテゴリを選択してください</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ old('course_id', $selectedCate ?? '') == $course->id ? 'selected' : '' }}>
                        {{ $course->title }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="course">コース(表示名)</label>
            <input type="text" name="course" id="course" class="form-control" value="{{ old('course') }}">
        </div>
        <div class="form-group">
            <label for="course_time">コースの時間(分)</label>
            <input type="text" name="course_time" id="course_time" class="form-control size_ss" value="{{ old('course_time') }}" placeholder="60"><div class="unit">分</div>
        </div>
        <div class="form-group">
            <label for="fee">料金</label>
            <input type="text" name="fee" id="fee" class="form-control" value="{{ old('fee') }}"  required><div class="unit">円</div>
        </div>
        <div class="form-group">
            <label for="text">説明文</label>
            <textarea name="text" class="form-control">{{ old('text') }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary"> 更新する </button>
    </form>
</section>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        var timerFlagCheckbox = document.getElementById('timer_flag');
        var dateFields = document.getElementById('date_fields');

        // チェックボックスの状態に応じて表示・非表示を切り替え
        timerFlagCheckbox.addEventListener('change', function () {
            if (timerFlagCheckbox.checked) {
                dateFields.style.display = 'block';
            } else {
                dateFields.style.display = 'none';
            }
        });
    });
</script>


</x-item-layout>
