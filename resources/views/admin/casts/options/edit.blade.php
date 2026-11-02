<x-admin-layout>


    <x-slot name="header">
         オプション設定
    </x-slot>

    <section>
        <form action="{{ route('casts.options.update', $cast->id) }}" method="POST">
            @csrf
            {{-- options[]が1つも送信されない場合とフォーム未送信を区別するためのフラグ --}}
            <input type="hidden" name="options_submitted" value="1">
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



</x-admin-layout>
