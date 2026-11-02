@props(['child', 'adminPreview' => false])

@php
    $teaserOn = $adminPreview ? false : $child->teaserBlocksMain();
@endphp

<div class="border p-6 bg-gray-50 mt-3">

    @if($child->published_at)
        <p class="text-sm mt-1 mb-4 whitespace-pre-wrap">{{ $child->published_at->format('Y/m/d H:i') }} UP</p>
    @endif
    @if($child->title)
        <h4 class="mb-4 font-semibold text-lg">{{ $child->title }}</h4>
    @endif
    @if($child->description)
        <p class="text-sm mt-1 mb-4 whitespace-pre-wrap">{{ $child->description }}</p>
    @endif
    @if($teaserOn)
        <p class="text-xs text-pink-800 mt-2">
            @if($child->teaser_promo_note)
                {{ $child->teaser_promo_note }}
            @endif
            @if($child->teaser_reveal_at)
                <span class="block mt-1 font-medium">本編・ぼかし解除予定: {{ $child->teaser_reveal_at->format('Y/m/d H:i') }}</span>
            @endif
        </p>
    @endif

    <div class="mt-2 space-y-2">
        @foreach($child->items as $item)
            @php
                $value   = $item->value ?? [];
                $imgUrl  = $value['url'] ?? null;
                $vidUrl  = $value['video_url'] ?? null;
                $embedUrl = $value['embed_url'] ?? null;

                // YouTube サムネイル
                $ytThumb = null;
                if ($embedUrl) {
                    preg_match('/(?:youtube\.com\/(?:watch\?v=|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $embedUrl, $m);
                    if (!empty($m[1])) {
                        $ytThumb = 'https://img.youtube.com/vi/' . $m[1] . '/mqdefault.jpg';
                    }
                }
            @endphp
            <div class="border p-6 bg-white">

                {{-- 画像 --}}
                @if($imgUrl)
                    @if($teaserOn)
                        @if($child->teaser_style === 'custom_image' && $child->teaser_image_path)
                            <img src="{{ $child->teaserImageUrl() }}" alt="teaser" class="mt-2 max-w-full h-auto border">
                        @else
                            <div class="relative inline-block max-w-full mt-2 overflow-hidden">
                                <img src="{{ $imgUrl }}" alt="" class="max-w-full h-auto border" style="filter: blur(16px); transform: scale(1.05);">
                            </div>
                        @endif
                    @else
                        <img src="{{ $imgUrl }}" alt="{{ $value['name'] ?? 'image' }}" class="mt-2 max-w-full h-auto border">
                    @endif
                @endif

                {{-- 動画 --}}
                @if($vidUrl)
                    @if($teaserOn)
                        @if($child->teaser_style === 'custom_image' && $child->teaser_image_path)
                            <img src="{{ $child->teaserImageUrl() }}" alt="teaser" class="mt-2 max-w-full h-auto border">
                        @else
                            <div class="relative inline-block mt-2 overflow-hidden">
                                <video src="{{ $vidUrl }}" class="max-w-full h-auto border" style="filter: blur(16px); transform: scale(1.05);" preload="metadata" muted playsinline></video>
                            </div>
                        @endif
                    @else
                        <video src="{{ $vidUrl }}" controls class="mt-2 max-w-full h-auto border" preload="metadata"></video>
                    @endif
                @endif

                {{-- 埋め込みリンク（YouTube等） --}}
                @if($embedUrl)
                    @if($teaserOn)
                        @if($child->teaser_style === 'custom_image' && $child->teaser_image_path)
                            <img src="{{ $child->teaserImageUrl() }}" alt="teaser" class="mt-2 max-w-full h-auto border">
                        @elseif($ytThumb)
                            <div class="relative inline-block mt-2 overflow-hidden">
                                <img src="{{ $ytThumb }}" alt="teaser" class="max-w-full h-auto border" style="filter: blur(16px); transform: scale(1.05);">
                            </div>
                        @else
                            <p class="text-sm text-gray-500 italic mt-2">ティザー期間中はこのブロックは非表示です。</p>
                        @endif
                    @else
                        @if($ytThumb)
                            <a href="{{ $embedUrl }}" target="_blank" rel="noopener" class="block mt-2">
                                <img src="{{ $ytThumb }}" alt="YouTube" class="max-w-full h-auto border">
                            </a>
                        @else
                            <a href="{{ $embedUrl }}" target="_blank" rel="noopener" class="text-blue-600 underline text-sm mt-2 block">{{ $embedUrl }}</a>
                        @endif
                    @endif
                @endif

                {{-- テキスト --}}
                @if($item->type === 'text')
                    @if($teaserOn)
                        <p class="text-sm text-gray-500 italic mt-2">ティザー期間中はこのブロックは非表示です。</p>
                    @elseif(!empty($value['body']))
                        <p class="text-sm whitespace-pre-wrap mt-2">{{ $value['body'] }}</p>
                    @endif
                @endif

                {{-- タイマー --}}
                @if($item->type === 'timer')
                    <x-fanclub.timer-item :value="$value" />
                @endif

            </div>
        @endforeach
    </div>
</div>
