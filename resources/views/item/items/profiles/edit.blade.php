<x-item-layout>


    <x-slot name="header">
         プロフィール設定
    </x-slot>

    <section>
        <form action="{{ route('items.profiles.update', $item->id) }}" method="POST">
            @csrf

            <div>
                @foreach ($profiles as $profile)
                    <label>
                        <input type="checkbox" name="profiles[]" value="{{ $profile->id }}"
                        {{ in_array($profile->id, $selectedProfiles) ? 'checked' : '' }}>
                        {{ $profile->title }}
                    </label><br>
                @endforeach
            </div>
            <button type="submit">更新</button>
        </form>

</section>





</x-item-layout>
