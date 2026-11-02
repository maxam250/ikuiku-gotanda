<x-admin-layout>

    <x-slot name="header">
         Q&A設定
    </x-slot>

    <section>
        <form action="{{ route('items.interviews.update', $item->id) }}" method="POST">
            @csrf

            <div>
                @foreach ($interviews as $interview)
                    <label>
                        <input type="checkbox" name="interviews[]" value="{{ $interview->id }}"
                        {{ in_array($interview->id, $selectedInterviews) ? 'checked' : '' }}>
                        {{ $interview->title }}
                    </label><br>
                @endforeach
            </div>
            <button type="submit">更新</button>
        </form>

    </section>

</x-admin-layout> 