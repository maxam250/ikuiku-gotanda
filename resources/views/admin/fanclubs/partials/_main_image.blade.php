{{--
  メイン画像アップロードフィールド
  変数: $fanclub (Fanclub model)
--}}
<div class="space-y-2">
    <label class="block text-sm font-medium text-gray-700 mb-1">
        メイン画像
        <span class="ml-2 text-xs font-normal text-gray-400">推奨アスペクト比 3：2（例: 900×600px）</span>
    </label>

    @if($fanclub->main_image_path)
        <div class="flex items-start gap-4 p-3 bg-gray-50 border rounded">
            <img src="{{ asset('storage/' . $fanclub->main_image_path) }}"
                 alt="メイン画像"
                 class="w-40 rounded border object-cover bg-white"
                 style="aspect-ratio:3/2;">
            <div class="text-xs text-gray-500 space-y-1">
                <p class="break-all">{{ basename($fanclub->main_image_path) }}</p>
                <label class="flex items-center gap-1 cursor-pointer text-red-600">
                    <input type="checkbox" name="remove_main_image" value="1"
                           {{ old('remove_main_image') ? 'checked' : '' }}>
                    画像を削除する
                </label>
            </div>
        </div>
    @endif

    <input type="file" name="main_image_file" accept="image/jpeg,image/png,image/webp"
           class="w-full border border-gray-300 rounded px-2 py-1 bg-white text-sm"
           onchange="(function(input){
               const p = document.getElementById('main-img-preview');
               if(!p) return;
               if(input.files && input.files[0]){
                   p.src = URL.createObjectURL(input.files[0]);
                   p.classList.remove('hidden');
               } else { p.classList.add('hidden'); }
           })(this)">
    <img id="main-img-preview" src="" alt="プレビュー"
         class="hidden mt-2 w-40 rounded border object-cover bg-gray-50"
         style="aspect-ratio:3/2;">
    @error('main_image_file')
        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
    @enderror
</div>
