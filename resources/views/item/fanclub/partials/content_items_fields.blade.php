@php
    $itemsValue = $itemsValue ?? old('items');
    if ($itemsValue === null && isset($record)) {
        $itemsValue = $record->items?->map(function ($item) {
            return [
                'type' => $item->type,
                'value' => $item->value,
                'sort' => $item->sort,
            ];
        })->values()->toArray();
    }
    if (empty($itemsValue)) {
        $itemsValue = [['type' => 'image', 'value' => ['body' => ''], 'sort' => 0]];
    }
    $typeOptions = [
        'image'  => '画像',
        'video'  => '動画',
        'text'   => 'テキスト',
        'link'   => '外部リンク',
    ];
    $initialCount = count($itemsValue);
@endphp

<div class="bg-yellow-50 border border-yellow-300 p-5 rounded-lg space-y-5">
    <label class="block text-sm font-semibold">コンテンツ詳細アイテム</label>

    <div id="items-container" class="space-y-4">

        @foreach($itemsValue as $index => $item)
            @php
                $existingValue     = is_array($item['value'] ?? null) ? $item['value'] : [];
                $existingPath      = $existingValue['path'] ?? null;
                $existingUrl       = $existingValue['url'] ?? null;
                $existingName      = $existingValue['name'] ?? null;
                $existingVideoPath = $existingValue['video_path'] ?? null;
                $existingVideoUrl  = $existingValue['video_url'] ?? null;
                $existingVideoName = $existingValue['video_name'] ?? null;
                $existingEmbedUrl  = $existingValue['embed_url'] ?? '';
                $ytThumb = null;
                if ($existingEmbedUrl) {
                    preg_match('/(?:youtube\.com\/(?:watch\?v=|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $existingEmbedUrl, $m);
                    if (!empty($m[1])) {
                        $ytThumb = 'https://img.youtube.com/vi/' . $m[1] . '/mqdefault.jpg';
                    }
                }
            @endphp

            @if(($item['type'] ?? '') === 'image')
                {{-- 画像ブロック --}}
                <div class="item-block bg-white border rounded-lg p-4 space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b">
                        <span class="text-sm font-medium text-gray-700">画像</span>
                        <button type="button" onclick="removeItemBlock(this)"
                                class="text-xs text-red-500 hover:text-red-700 border border-red-300 rounded px-2 py-0.5">削除</button>
                    </div>
                    <input type="hidden" name="items[{{ $index }}][type]" value="image">
                    <input type="hidden" name="items[{{ $index }}][sort]" value="{{ $index }}">
                    @if($existingPath)
                        <input type="hidden" name="items[{{ $index }}][value][path]" value="{{ $existingPath }}">
                        <input type="hidden" name="items[{{ $index }}][value][url]"  value="{{ $existingUrl }}">
                        <input type="hidden" name="items[{{ $index }}][value][name]" value="{{ $existingName }}">
                        <div class="flex items-start gap-3 p-2 bg-gray-50 rounded border">
                            <img src="{{ $existingUrl ?? \Illuminate\Support\Facades\Storage::disk('public')->url($existingPath) }}"
                                 alt="現在の画像"
                                 class="h-16 w-24 border rounded object-cover bg-white flex-shrink-0">
                            <div class="text-xs text-gray-500 space-y-1">
                                <p class="break-all">{{ $existingName ?? $existingPath }}</p>
                                <label class="flex items-center gap-1 cursor-pointer text-red-600">
                                    <input type="checkbox" name="items[{{ $index }}][remove_image]" value="1">
                                    画像を削除
                                </label>
                            </div>
                        </div>
                    @endif
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">画像ファイルを選択</label>
                        <input type="file" name="items[{{ $index }}][image_file]" accept="image/*"
                               class="w-full border rounded px-2 py-1 bg-white text-sm"
                               onchange="previewImage(this, 'img-preview-{{ $index }}')">
                        <img id="img-preview-{{ $index }}" src="" alt="" class="h-20 w-28 mt-2 border rounded object-cover bg-gray-50 hidden">
                    </div>
                </div>

            @else
                {{-- 動画・埋め込み・テキストブロック --}}
                <div class="item-block bg-white border rounded-lg p-4 space-y-4">
                    <div class="flex items-center justify-between pb-2 border-b">
                        <div class="flex items-center gap-3">
                            <label class="text-sm font-medium whitespace-nowrap">タイプ</label>
                            <select name="items[{{ $index }}][type]" class="border px-2 py-1 text-sm rounded">
                                @foreach($typeOptions as $value => $label)
                                    <option value="{{ $value }}" @selected(($item['type'] ?? '') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" onclick="removeItemBlock(this)"
                                class="text-xs text-red-500 hover:text-red-700 border border-red-300 rounded px-2 py-0.5">削除</button>
                    </div>
                    <input type="hidden" name="items[{{ $index }}][sort]" value="{{ $index }}">
                    @if($existingVideoPath)
                        <input type="hidden" name="items[{{ $index }}][value][video_path]" value="{{ $existingVideoPath }}">
                        <input type="hidden" name="items[{{ $index }}][value][video_url]"  value="{{ $existingVideoUrl }}">
                        <input type="hidden" name="items[{{ $index }}][value][video_name]" value="{{ $existingVideoName }}">
                    @endif

                    {{-- 動画ファイル --}}
                    <div class="space-y-2">
                        <label class="block text-xs font-medium text-gray-700">動画ファイル（100MB以下）</label>
                        @if($existingVideoPath)
                            <div class="flex items-start gap-3 p-2 bg-gray-50 rounded border">
                                <video src="{{ $existingVideoUrl ?? \Illuminate\Support\Facades\Storage::disk('public')->url($existingVideoPath) }}"
                                       class="h-16 w-24 border rounded object-cover bg-black flex-shrink-0"
                                       preload="metadata"></video>
                                <div class="text-xs text-gray-500 space-y-1">
                                    <a href="{{ $existingVideoUrl ?? \Illuminate\Support\Facades\Storage::disk('public')->url($existingVideoPath) }}" target="_blank" class="underline break-all">{{ $existingVideoName ?? $existingVideoPath }}</a>
                                    <label class="flex items-center gap-1 cursor-pointer text-red-600">
                                        <input type="checkbox" name="items[{{ $index }}][remove_video]" value="1">
                                        動画を削除
                                    </label>
                                </div>
                            </div>
                        @endif
                        <input type="file" name="items[{{ $index }}][video_file]" accept="video/mp4,video/quicktime,video/x-msvideo,video/webm,.mp4,.mov,.avi,.webm"
                               class="w-full border rounded px-2 py-1 bg-white text-sm"
                               onchange="previewVideo(this, 'vid-preview-{{ $index }}')">
                        <video id="vid-preview-{{ $index }}" class="h-16 w-24 border rounded object-cover bg-black hidden" preload="metadata"></video>
                    </div>

                    {{-- 外部埋め込みリンク --}}
                    <div class="space-y-2">
                        <label class="block text-xs font-medium text-gray-700">外部埋め込みリンク（YouTube など）</label>
                        @if($ytThumb)
                            <div class="flex items-start gap-3 p-2 bg-gray-50 rounded border">
                                <img src="{{ $ytThumb }}" alt="YouTube サムネイル"
                                     class="h-16 w-24 border rounded object-cover bg-gray-50 flex-shrink-0">
                                <div class="text-xs text-gray-500 break-all">
                                    <a href="{{ $existingEmbedUrl }}" target="_blank" class="underline">{{ $existingEmbedUrl }}</a>
                                </div>
                            </div>
                        @endif
                        <input type="url" name="items[{{ $index }}][value][embed_url]"
                               value="{{ $existingEmbedUrl }}"
                               class="w-full border rounded px-2 py-1 text-sm"
                               placeholder="https://www.youtube.com/watch?v=...">
                    </div>

                    {{-- テキスト本文 --}}
                    <div class="space-y-2">
                        <label class="block text-xs font-medium text-gray-700">テキスト本文</label>
                        <textarea name="items[{{ $index }}][value][body]" rows="6"
                                  class="w-full border rounded px-2 py-1 text-sm min-h-[120px]"
                                  placeholder="テキスト本文">{{ $existingValue['body'] ?? '' }}</textarea>
                    </div>
                </div>
            @endif

        @endforeach

    </div>{{-- #items-container --}}

    {{-- 追加ボタン --}}
    <div class="flex gap-3 pt-2">
        <button type="button" id="add-image-btn"
                class="px-4 py-2 border border-gray-400 rounded text-sm bg-white hover:bg-gray-50">
            ＋ 画像を追加
        </button>
        <button type="button" id="add-media-btn"
                class="px-4 py-2 border border-gray-400 rounded text-sm bg-white hover:bg-gray-50">
            ＋ 動画・埋め込みを追加
        </button>
    </div>

</div>

{{-- 新規画像ブロック テンプレート --}}
<template id="image-item-template">
    <div class="item-block bg-white border rounded-lg p-4 space-y-3">
        <div class="flex items-center justify-between pb-2 border-b">
            <span class="text-sm font-medium text-gray-700">画像</span>
            <button type="button" onclick="removeItemBlock(this)"
                    class="text-xs text-red-500 hover:text-red-700 border border-red-300 rounded px-2 py-0.5">削除</button>
        </div>
        <input type="hidden" name="items[__IDX__][type]" value="image">
        <input type="hidden" name="items[__IDX__][sort]" value="__IDX__">
        <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">画像ファイルを選択</label>
            <input type="file" name="items[__IDX__][image_file]" accept="image/*"
                   class="w-full border rounded px-2 py-1 bg-white text-sm"
                   onchange="previewImage(this, 'img-preview-__IDX__')">
            <img id="img-preview-__IDX__" src="" alt="" class="h-20 w-28 mt-2 border rounded object-cover bg-gray-50 hidden">
        </div>
    </div>
</template>

{{-- 新規動画・埋め込みブロック テンプレート --}}
<template id="media-item-template">
    <div class="item-block bg-white border rounded-lg p-4 space-y-4">
        <div class="flex items-center justify-between pb-2 border-b">
            <div class="flex items-center gap-3">
                <label class="text-sm font-medium whitespace-nowrap">タイプ</label>
                <select name="items[__IDX__][type]" class="border px-2 py-1 text-sm rounded">
                    <option value="video">動画</option>
                    <option value="text">テキスト</option>
                    <option value="link">外部リンク</option>
                </select>
            </div>
            <button type="button" onclick="removeItemBlock(this)"
                    class="text-xs text-red-500 hover:text-red-700 border border-red-300 rounded px-2 py-0.5">削除</button>
        </div>
        <input type="hidden" name="items[__IDX__][sort]" value="__IDX__">
        <div class="space-y-2">
            <label class="block text-xs font-medium text-gray-700">動画ファイル（100MB以下）</label>
            <input type="file" name="items[__IDX__][video_file]" accept="video/mp4,video/quicktime,video/x-msvideo,video/webm,.mp4,.mov,.avi,.webm"
                   class="w-full border rounded px-2 py-1 bg-white text-sm"
                   onchange="previewVideo(this, 'vid-preview-__IDX__')">
            <video id="vid-preview-__IDX__" class="h-16 w-24 border rounded object-cover bg-black hidden" preload="metadata"></video>
        </div>
        <div class="space-y-2">
            <label class="block text-xs font-medium text-gray-700">外部埋め込みリンク（YouTube など）</label>
            <input type="url" name="items[__IDX__][value][embed_url]" value=""
                   class="w-full border rounded px-2 py-1 text-sm"
                   placeholder="https://www.youtube.com/watch?v=...">
        </div>
        <div class="space-y-2">
            <label class="block text-xs font-medium text-gray-700">テキスト本文</label>
            <textarea name="items[__IDX__][value][body]" rows="6"
                      class="w-full border rounded px-2 py-1 text-sm min-h-[120px]"
                      placeholder="テキスト本文"></textarea>
        </div>
    </div>
</template>

<script>
(function () {
    let nextIdx = {{ $initialCount }};

    document.getElementById('add-image-btn').addEventListener('click', function () {
        addBlock('image-item-template');
    });

    document.getElementById('add-media-btn').addEventListener('click', function () {
        addBlock('media-item-template');
    });

    function addBlock(templateId) {
        const template = document.getElementById(templateId);
        const container = document.getElementById('items-container');
        const html = template.innerHTML.replace(/__IDX__/g, nextIdx);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html;
        container.appendChild(wrapper.firstElementChild);
        nextIdx++;
    }
})();

function removeItemBlock(btn) {
    btn.closest('.item-block').remove();
}

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview) return;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '';
        preview.classList.add('hidden');
    }
}

function previewVideo(input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview) return;
    if (input.files && input.files[0]) {
        preview.src = URL.createObjectURL(input.files[0]);
        preview.classList.remove('hidden');
    } else {
        preview.src = '';
        preview.classList.add('hidden');
    }
}
</script>
