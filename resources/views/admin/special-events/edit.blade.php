<x-admin-layout>
    <x-slot name="header">
    特設イベント編集
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b">
                <h3 class="text-lg font-semibold text-gray-800">特設イベント編集</h3>
                <a href="{{ route('admin.special-events.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-md text-sm font-medium">
                    <i class="fas fa-arrow-left mr-2"></i> 一覧に戻る
                </a>
            </div>

           <!-- Body -->
            <div class="p-6">
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-300 rounded-md">
                            <p class="text-sm font-semibold text-red-700 mb-2">入力内容にエラーがあります：</p>
                            <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.special-events.update', $specialEvent) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')
                        <div>
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $specialEvent->is_active) ? 'checked' : '' }}>
                                <label for="is_active" class="form-check-label">公開する</label>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700">タイトル <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('title') is-invalid @enderror" value="{{ old('title', $specialEvent->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div>
                                    <label for="event_date" class="block text-sm font-medium text-gray-700">開催日 <span class="text-danger">*</span></label>
                                    <input type="date" name="event_date" id="event_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('event_date') is-invalid @enderror" value="{{ old('event_date', $specialEvent->event_date->format('Y-m-d')) }}" required>
                                    @error('event_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                        </div>


                    <!-- 開始・終了時間 -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                            <div>
                                <label for="start_hour" class="block text-sm font-medium text-gray-700">開始時間（時） <span class="text-danger">*</span></label>
                                <select name="start_hour" id="start_hour" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('start_hour') is-invalid @enderror" required>
                                    <option value="">時</option>
                                    @for($hour = 0; $hour < 24; $hour++)
                                        <option value="{{ $hour }}" {{ old('start_hour', $specialEvent->start_hour) == $hour ? 'selected' : '' }}>
                                            {{ $hour }}時
                                        </option>
                                    @endfor
                                </select>
                                @error('start_hour')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="start_minute" class="block text-sm font-medium text-gray-700">開始時間（分） <span class="text-danger">*</span></label>
                                <select name="start_minute" id="start_minute" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('start_minute') is-invalid @enderror" required>
                                    <option value="">分</option>
                                    @for($minute = 0; $minute < 60; $minute += 15)
                                        <option value="{{ $minute }}" {{ old('start_minute', $specialEvent->start_minute) == $minute ? 'selected' : '' }}>
                                            {{ $minute }}分
                                        </option>
                                    @endfor
                                </select>
                                @error('start_minute')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="end_hour" class="block text-sm font-medium text-gray-700">終了時間（時） <span class="text-danger">*</span></label>
                                <select name="end_hour" id="end_hour" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('end_hour') is-invalid @enderror" required>
                                    <option value="">時</option>
                                    @for($hour = 0; $hour < 24; $hour++)
                                        <option value="{{ $hour }}" {{ old('end_hour', $specialEvent->end_hour) == $hour ? 'selected' : '' }}>
                                            {{ $hour }}時
                                        </option>
                                    @endfor
                                </select>
                                @error('end_hour')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div>
                                <label for="end_minute" class="block text-sm font-medium text-gray-700">終了時間（分） <span class="text-danger">*</span></label>
                                <select name="end_minute" id="end_minute" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('end_minute') is-invalid @enderror" required>
                                    <option value="">分</option>
                                    @for($minute = 0; $minute < 60; $minute += 15)
                                        <option value="{{ $minute }}" {{ old('end_minute', $specialEvent->end_minute) == $minute ? 'selected' : '' }}>
                                            {{ $minute }}分
                                        </option>
                                    @endfor
                                </select>
                                @error('end_minute')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-700">参加キャスト</label>
                            <div class="grid grid-cols-3 lg:grid-cols-6 gap-4">
                                @foreach($items as $item)
                                <div class="w-full">
                                        <div class="form-check">
                                            <input type="checkbox" name="item_ids[]" value="{{ $item->id }}" id="item_{{ $item->id }}" class="form-check-input" {{ in_array($item->id, old('item_ids', $selectedItems)) ? 'checked' : '' }}>
                                            <label for="item_{{ $item->id }}" class="form-check-label">
                                                {{ $item->item_name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('item_ids')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">内容 <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" class="tinymce mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('description') is-invalid @enderror" required>{{ old('description', $specialEvent->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="main_image" class="block text-sm font-medium text-gray-700">メイン画像</label>
                            @if($specialEvent->main_image)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $specialEvent->main_image) }}" alt="現在の画像" class="img-thumbnail" style="max-width: 200px;">
                                    <br><small class="text-muted">現在の画像</small>
                                </div>
                            @endif
                            <input type="file" name="main_image" id="main_image" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('main_image') is-invalid @enderror" accept="image/*">
                            @error('main_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">JPEG、PNG、JPG、GIF形式、2MB以下（変更する場合のみ選択）</small>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <label for="fee" class="block text-sm font-medium text-gray-700">料金</label>

                                <div class="flex items-baseline">
                                <input type="number" name="fee" id="fee" class="mt-1 mr-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('fee') is-invalid @enderror" value="{{ old('fee', $specialEvent->fee) }}" min="0">円
                                </div>
                                @error('fee')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="fee_note" class="block text-sm font-medium text-gray-700">料金注意事項</label>
                            <textarea name="fee_note" id="fee_note" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('fee_note') is-invalid @enderror" rows="3">{{ old('fee_note', $specialEvent->fee_note) }}</textarea>
                            @error('fee_note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="venue" class="block text-sm font-medium text-gray-700">場所</label>
                            <input type="text" name="venue" id="venue" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('venue') is-invalid @enderror" value="{{ old('venue', $specialEvent->venue) }}">
                            @error('venue')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="access_info" class="block text-sm font-medium text-gray-700">アクセス案内</label>
                            <textarea name="access_info" id="access_info" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('access_info') is-invalid @enderror" rows="3">{{ old('access_info', $specialEvent->access_info) }}</textarea>
                            @error('access_info')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="map_url" class="block text-sm font-medium text-gray-700">Google Map URL</label>
                            <input type="url" name="map_url" id="map_url" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('map_url') is-invalid @enderror" value="{{ old('map_url', $specialEvent->map_url) }}">
                            @error('map_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="external_link" class="block text-sm font-medium text-gray-700">外部リンク</label>
                            <input type="url" name="external_link" id="external_link" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('external_link') is-invalid @enderror" value="{{ old('external_link', $specialEvent->external_link) }}">
                            @error('external_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700">備考・注意事項</label>
                            <textarea name="notes" id="notes" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('notes') is-invalid @enderror" rows="3">{{ old('notes', $specialEvent->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">


                                <div>
                                    <div class="form-check">
                                        <input type="checkbox" name="has_timer" id="has_timer" class="form-check-input" value="1" {{ old('has_timer', $specialEvent->has_timer) ? 'checked' : '' }}>
                                        <label for="has_timer" class="form-check-label">タイマー表示を有効にする</label>
                                    </div>
                                </div>
                        </div>

                        <div class="row" id="timer_settings" style="display: {{ old('has_timer', $specialEvent->has_timer) ? 'block' : 'none' }};">
                            <div class="col-md-6">
                                <div>
                                    <label for="timer_start">表示開始日</label>
                                    <input type="date" name="timer_start" id="timer_start" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('timer_start') is-invalid @enderror" value="{{ old('timer_start', $specialEvent->timer_start ? $specialEvent->timer_start->format('Y-m-d') : '') }}">
                                    @error('timer_start')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div>
                                    <label for="timer_end">表示終了日</label>
                                    <input type="date" name="timer_end" id="timer_end" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm @error('timer_end') is-invalid @enderror" value="{{ old('timer_end', $specialEvent->timer_end ? $specialEvent->timer_end->format('Y-m-d') : '') }}">
                                    @error('timer_end')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> 更新
                            </button>
                            <a href="{{ route('admin.special-events.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> キャンセル
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

    <script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('js/admin-tinymce.js') }}"></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // TinyMCE初期化
    initAdminTinyMCE(
        '#description',
        '{{ asset("js/tinymce/skins/ui/" . ($siteSettings?->tinymce_skin ?? "oxide")) }}',
        '{{ asset("js/tinymce/skins/content/" . (($siteSettings?->tinymce_skin ?? "oxide") === "oxide-dark" ? "dark" : "default") . "/content.min.css") }}'
    );

    const hasTimerCheckbox = document.getElementById('has_timer');
    const timerSettings = document.getElementById('timer_settings');

    hasTimerCheckbox.addEventListener('change', function() {
        if (this.checked) {
            timerSettings.style.display = 'block';
        } else {
            timerSettings.style.display = 'none';
        }
    });
});
</script>
</x-admin-layout>
