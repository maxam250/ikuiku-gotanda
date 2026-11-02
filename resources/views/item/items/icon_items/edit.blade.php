<x-item-layout>


    <x-slot name="header">
         キャストアイコン設定
    </x-slot>

    <section>
        <form action="{{ route('items.icon_items.update', $item->id) }}" method="POST">
            @csrf
            <div>
                @foreach ($icon_items as $icon_item)
                    <label>
                        <input type="checkbox" name="icon_items[]" value="{{ $icon_item->id }}"
                        {{ in_array($icon_item->id, $selectedIconCast) ? 'checked' : '' }}>
                        {{ $icon_item->title }}
                    </label><br>
                @endforeach
            </div>
            <button type="submit">更新</button>
        </form>

</section>



</x-item-layout>
