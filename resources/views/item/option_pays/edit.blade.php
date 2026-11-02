<x-item-layout>


    <x-slot name="header">
    オプションPAY編集
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

    <form action="{{ route('item.option_pays.update', $option_pays->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
@php
    $selectedCate = $option_pays->cate;
@endphp
        <label for="cate">カテゴリ</label>
            <select name="cate" id="cate" class="form-control" required>
                <option value="" disabled {{ old('cate', $selectedCate ?? '') == '' ? 'selected' : '' }}>カテゴリを選択してください</option>
                @foreach($optionCates as $cate)
                    <option value="{{ $cate->id }}" {{ old('cate', $selectedCate ?? '') == $cate->id ? 'selected' : '' }}>
                        {{ $cate->title }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="title">オプション名</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ $option_pays->title }}"  required>
        </div>
        <div class="form-group">
            <label for="body">説明文</label>
            <textarea name="body" class="form-control">{{ $option_pays->body }}</textarea>
        </div>
        <div class="form-group">
            <label for="price">金額</label>
            <input type="text" name="price" id="price" class="form-control" value="{{ $option_pays->price }}"><div class="unit">円</div>
        </div>

        <button type="submit" class="btn btn-primary"> 更新する </button>
    </form>

</section>



</x-item-layout>
