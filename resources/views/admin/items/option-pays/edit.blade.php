<x-admin-layout>


    <x-slot name="header">
         オプションPAY設定
    </x-slot>

    <section>
        <form action="{{ route('items.option_pays.update', $item->id) }}" method="POST">
            @csrf
            <div>
                @foreach ($option_pays as $option_pay)
                    <label>
                        <input type="checkbox" name="option_pays[]" value="{{ $option_pay->id }}"
                        {{ in_array($option_pay->id, $selectedOptionPays) ? 'checked' : '' }}>
                        {{ $option_pay->title }}
                    </label><br>
                @endforeach
            </div>
            <button type="submit">更新</button>
        </form>

</section>



</x-admin-layout>
