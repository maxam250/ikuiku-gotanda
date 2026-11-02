<x-item-layout>


    <x-slot name="header">
         オプション設定
    </x-slot>

    <section>
        <form action="{{ route('items.options.update', $item->id) }}" method="POST">
            @csrf
            <div>
                @foreach ($options as $option)
                    <label>
                        <input type="checkbox" name="options[]" value="{{ $option->id }}"
                        {{ in_array($option->id, $selectedOptions) ? 'checked' : '' }}>
                        {{ $option->title }}
                    </label><br>
                @endforeach
            </div>
            <button type="submit">更新</button>
        </form>

</section>



</x-item-layout>
