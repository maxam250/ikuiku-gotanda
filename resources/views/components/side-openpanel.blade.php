<div x-data="{ open: false }" class="fixed inset-y-0 right-0 flex items-center z-9999" style="z-index: 998">
    <!-- 開閉ボタン -->
    <button @click="open = !open"
        class="bg-yellow-400 text-white px-2 py-1 rounded-l-lg shadow-md hover:bg-yellow-500 transition-all duration-300 line-height-1"
        style="transform: translateX(0%); position: absolute; right: 0; top: 50%; transform: translateY(-50%); z-index:999; line-height:1.2;font-size:0.7rem;        ">
        <i class="fas" :class="open ? 'fa-chevron-right' : 'fa-chevron-left'"></i>&nbsp;
        {{ $shops->shop_name }}について
    </button>

    <!-- パネル本体 -->
    <div x-show="open"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="w-full h-[80vh] shadow-lg border-l border-gray-300 p-4 rounded-2xl"
         style="z-inded:98;margin-right: 40px;background:rgba(255,255,255,0.98);">
         
        {{-- @includeでコンテンツ挿入 --}}
        @include('aboutus')
    </div>
</div>
