{{-- キャストアイコン（旧 .cast_icon_L / .cast_icon_R：1つ目を左上、2つ目を右下） --}}
@php $gtdIcons = $item->icon_items->filter(fn ($icon) => $icon->filename)->take(2)->values(); @endphp
@foreach($gtdIcons as $i => $icon)
<span class="gtd-cast-icon {{ $i === 0 ? 'gtd-cast-icon--l' : 'gtd-cast-icon--r' }}">
    <img src="{{ Storage::url('icon_item/' . $icon->filename) }}" alt="{{ $icon->title }}" loading="lazy">
</span>
@endforeach
