<x-item-layout>


    <x-slot name="header">
    プロフィール編集
    </x-slot>


    <div class="back">
        <a href="{{ route('item.profiles.index') }}" class="btn-primary">戻る</a>
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

    <form action="{{ route('item.profiles.update', $profile->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="title">項目名</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ $profile->title }}"  required>
        </div>

        <button type="submit" class="btn btn-primary"> 更新する </button>
    </form>

</section>



</x-item-layout>
