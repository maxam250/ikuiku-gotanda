@php
    $teaserActive = old('teaser_enabled') !== null
        ? (old('teaser_enabled') === '1')
        : (bool) $child?->teaser_enabled;
    $publishedInitial = old('publish_status') !== null
        ? (old('publish_status') === 'public')
        : (bool) $child?->published_at;
    $scheduledValue = old('scheduled_at',
        $child?->teaser_enabled
            ? optional($child?->teaser_reveal_at)->format('Y-m-d\TH:i')
            : optional($child?->scheduled_at)->format('Y-m-d\TH:i')
    );
@endphp

<div>
    <label class="block text-sm mb-1">ポストタイトル <span class="text-red-600">※必須</span></label>
    <input type="text" name="title" value="{{ old('title', $child?->title) }}" class="w-full border px-3 py-2" placeholder="例: 第2弾 / making" required maxlength="255">
</div>

<div>
    <label class="block text-sm mb-1">ポストの説明（任意）</label>
    <textarea name="description" rows="6" class="w-full border px-3 py-2 min-h-[140px]">{{ old('description', $child?->description) }}</textarea>
</div>

<div>
    <label class="block text-sm mb-1">公開設定</label>
    <input type="hidden" name="publish_status" id="child_publish_status" value="{{ $publishedInitial ? 'public' : 'private' }}">
    <button type="button" id="child_publish_btn"
        class="af-btn af-btn-primary af-btn-sm"
        onclick="
            var inp = document.getElementById('child_publish_status');
            var btn = document.getElementById('child_publish_btn');
            if (inp.value === 'public') {
                inp.value = 'private';
                btn.textContent = '非公開';
                btn.className = 'af-btn af-btn-reset af-btn-sm';
            } else {
                inp.value = 'public';
                btn.textContent = '公開中';
                btn.className = 'af-btn af-btn-primary af-btn-sm';
            }
        ">
        {{ $publishedInitial ? '公開中' : '非公開' }}
    </button>
    <p class="mt-1 text-xs text-gray-500">ティーザーなしで本編公開日時を設定した場合は、そちらが優先されます。</p>
</div>

<div>
    <label class="block text-xs mb-1">本編公開日時</label>
    <input type="datetime-local" name="scheduled_at" value="{{ $scheduledValue }}" class="w-full border px-3 py-2">
    <p class="mt-1 text-xs text-gray-500">ティーザーなし: 予約投稿 ／ ティーザーあり: ティーザー解除日時</p>
</div>

<div class="border rounded">
    <button type="button" id="teaser_toggle_btn"
        class="w-full flex items-center justify-between px-4 py-3 text-left font-medium hover:bg-gray-50"
        onclick="
            var section = document.getElementById('teaser_section');
            var inp = document.getElementById('teaser_enabled_input');
            var icon = document.getElementById('teaser_icon');
            if (section.classList.contains('d-none')) {
                section.classList.remove('d-none');
                inp.value = '1';
                icon.textContent = '▲';
            } else {
                section.classList.add('d-none');
                inp.value = '0';
                icon.textContent = '▼';
            }
        ">
        <span>ティーザー設定（公開前のあおり・ぼかし）</span>
        <span id="teaser_icon">{{ $teaserActive ? '▲' : '▼' }}</span>
    </button>
    <input type="hidden" name="teaser_enabled" id="teaser_enabled_input" value="{{ $teaserActive ? '1' : '0' }}">
    <div id="teaser_section" class="px-4 pb-4 space-y-3 border-t {{ $teaserActive ? '' : 'd-none' }}">
        <p class="text-xs text-gray-600 pt-3">有効にすると、本編公開日時（ティーザー解除）まで画像をぼかすか、別画像を表示します。</p>
        <div>
            <label class="block text-xs mb-1">演出</label>
            <select name="teaser_style" class="w-full border px-3 py-2">
                <option value="blur" @selected(old('teaser_style', $child?->teaser_style ?? 'blur') === 'blur')>本編画像をぼかす</option>
                <option value="custom_image" @selected(old('teaser_style', $child?->teaser_style) === 'custom_image')>ティーザー専用画像を表示</option>
            </select>
        </div>
        <div>
            <label class="block text-xs mb-1">あおり文（例: ○月○日に本編アップ予定）</label>
            <input type="text" name="teaser_promo_note" value="{{ old('teaser_promo_note', $child?->teaser_promo_note) }}" class="w-full border px-3 py-2" maxlength="500">
        </div>
        <div>
            <label class="block text-xs mb-1">ティーザー画像</label>
            <input type="file" name="teaser_image_file" accept="image/*" class="w-full border px-2 py-1 bg-white">
            @if($child?->teaser_image_path)
                <div class="flex items-start gap-3 mt-1">
                    <img src="{{ $child->teaserImageUrl() }}" alt="ティーザー画像"
                         class="h-16 w-24 border rounded object-cover bg-gray-50 flex-shrink-0">
                    <div class="text-xs text-gray-500">
                        <a href="{{ $child->teaserImageUrl() }}" target="_blank" rel="noopener" class="underline">表示</a>
                        <label class="flex items-center gap-1 mt-1 cursor-pointer">
                            <input type="checkbox" name="remove_teaser_image" value="1">
                            ティーザー画像を削除
                        </label>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@include('item.fanclub.partials.content_items_fields', ['record' => $child])
