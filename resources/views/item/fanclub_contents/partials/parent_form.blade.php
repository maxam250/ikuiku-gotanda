<div>
    <label class="block text-sm mb-1">チャンネルタイトル</label>
    <input type="text" name="title" value="{{ old('title', $content?->title) }}" class="w-full border px-3 py-2" required>
</div>

<div>
    <label class="block text-sm mb-1">説明</label>
    <textarea name="description" rows="3" class="w-full border px-3 py-2">{{ old('description', $content?->description) }}</textarea>
</div>

<div>
    <label class="block text-sm mb-1">カテゴリ</label>
    <select name="category_id" class="w-full border px-3 py-2">
        <option value="">未選択</option>
        @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected((string)old('category_id', $content?->category_id) === (string)$category->id)>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
    @if(!isset($hideNewCategory) || !$hideNewCategory)
        <input type="text" name="new_category_name" value="{{ old('new_category_name') }}" placeholder="新規カテゴリ名（任意）" class="mt-2 w-full border px-3 py-2">
    @endif
</div>

@php
    $publishedInitial = !empty(old('publish_status'))
        ? (old('publish_status') === 'public')
        : (bool) $content?->published_at;
@endphp

<div>
    <label class="block text-sm mb-1">公開設定</label>
    <input type="hidden" name="publish_status" id="publish_status_input" value="{{ $publishedInitial ? 'public' : 'private' }}">
    <button type="button" id="publish_toggle_btn"
        class="af-btn af-btn-primary af-btn-sm"
        onclick="
            var inp = document.getElementById('publish_status_input');
            var btn = document.getElementById('publish_toggle_btn');
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
    <p class="mt-1 text-xs text-gray-500">予約公開日時を設定した場合は、そちらが優先されます。</p>
</div>

<div>
    <label class="block text-xs mb-1">予約公開日時</label>
    <input type="datetime-local" name="scheduled_at" value="{{ old('scheduled_at', optional($content?->scheduled_at)->format('Y-m-d\TH:i')) }}" class="w-full border px-3 py-2">
</div>

<div>
    <label class="block text-xs mb-1">投稿メモ</label>
    <input type="text" name="next_release_at" value="{{ old('next_release_at', $content?->next_release_at) }}" maxlength="512" placeholder="例: 毎週金曜更新 など" class="w-full border px-3 py-2">
</div>

<p class="text-xs text-gray-600">本文・画像などは「ポスト」で追加・編集します（1チャンネルに対して複数）。</p>
