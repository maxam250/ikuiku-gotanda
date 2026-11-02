<x-item-layout>


<x-slot name="header">
コース編集
</x-slot>

<div class="back">
    <a href="{{ route('item.options.index') }}" class="btn-primary">戻る</a>
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
    <form action="{{ route('item.courses.update', $course->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <div class="form-group">
                <label for="active">表示</label>
                <select name="active" id="active">
                    <option value="0" {{ $course->active == 0 ? 'selected' : '' }}>表示なし</option>
                    <option value="1" {{ $course->active == 1 ? 'selected' : '' }}>表示</option>
                </select>
            </div>
            <div class="form-group">
                <label for="title">コース名</label>
                <input type="text" name="title" class="form-control size_m" value="{{ $course->title }}" required>
            </div>
            <div class="form-group">
                <label for="body">コメント</label>
                <textarea name="body" class="form-control">{{ $course->body }}</textarea>
            </div>
        </div>
        <button type="submit" class="btn">更新する</button>

</section>



</x-item-layout>
