<x-item-layout>


    <x-slot name="header">
         キャストアイコン管理画面
    </x-slot>



    <div class="back">
        <a href="{{ route('item.icon_items.index') }}" class="btn-primary">戻る</a>
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
    <form action="{{ route('item.icon_items.update', $icon_item->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">アイコン名</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ $icon_item->title }}"  required>
        </div>
        <div class="form-group">
            <label for="filename">アイコン画像</label>
            <div>
            @if($icon_item->filename)
            <img src="{{ asset('uploads/icon_item/' . $icon_item->filename) }}" alt="" class="w_100px mb-3">
            @endif
            <input type="file" name="filename" class="form-control-file">
            </div>
        </div>

        <button type="submit" class="af-btn af-btn-primary">更新</button>
    </form>

</section>



</x-item-layout>
