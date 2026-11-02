<x-item-layout>


    <x-slot name="header">
         コース管理画面
    </x-slot>

    <section>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('item.course_fees.update', $courseFee->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
@php
    $selectedCate = $courseFee->course_id;
@endphp
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
            <label for="course">コース</label>
            <input type="text" name="course" id="course" class="form-control" value="{{ $courseFee->course }}">
        </div>
        <div class="form-group">
            <label for="course_time">コースの時間(分)</label>
            <input type="text" name="course_time" id="course_time" class="form-control" value="{{ $courseFee->course_time }}">
        </div>
        <div class="form-group">
            <label for="fee">料金</label>
            <input type="text" name="fee" id="fee" class="form-control" value="{{ $courseFee->fee }}"  required> 円
        </div>
        <div class="form-group">
            <label for="text">説明文</label>
            <textarea name="text" class="form-control">{{ $courseFee->text }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary"> 更新する </button>
    </form>

</section>



</x-item-layout>
