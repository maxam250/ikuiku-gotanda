<section id="top_gallery" class="top-section">
    <div class="top-section-inner">
        <h2 class="section-title">
            <span class="section-title-en">GALLERY</span>
            <span class="section-title-ja">ギャラリー</span>
        </h2>
        <div class="grid grid-cols-3 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-3 gap-6 lg:gap-8">
        @foreach ($galleries as $gallery)
            @php
                $firstPhoto = $gallery->images->first(fn($img) => $img->type !== 'video');
                $hasVideo   = $gallery->images->contains('type', 'video');
                $hasPhoto   = $firstPhoto !== null;
            @endphp
            <div class="group">
                <a href="{{ route('gallery.show', $gallery->id) }}" class="block bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-slate-200/50">
                    <div class="relative aspect-square overflow-hidden">
                        @if ($hasPhoto)
                            <img src="{{ Storage::url('gallery/'.$firstPhoto->filename) }}" alt="{{ $gallery->title }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                        @else
                            <div class="w-full h-full bg-slate-100 flex items-center justify-center text-slate-400" style="font-size:5rem;">
                                <i class="fas fa-{{ $hasVideo ? 'film' : 'image' }}"></i>
                            </div>
                        @endif
                        {{-- 動画バッジ --}}
                        @if ($hasVideo)
                            <div class="absolute top-2 left-2 bg-black/60 text-white text-xs px-2 py-1 rounded-full flex items-center gap-1">
                                <i class="fas fa-film text-pink-400"></i>
                                <span>動画あり</span>
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-bold text-slate-800 mb-2 line-clamp-2">{{ $gallery->title }}</h3>
                        <p class="text-sm text-slate-600">{{ $gallery->created_at ? $gallery->created_at->format('Y/m/d') : '日付不明' }}</p>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
        <div class="top-section-more">
            <a href="{{ route('gallery.index') }}" class="top-more-link">もっと見る &rsaquo;</a>
        </div>
    </div>
</section>
