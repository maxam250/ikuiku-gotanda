{{-- 
    管理画面フォームレイアウト使用例
    
    このファイルは参考用のサンプルです。
    実際のedit.blade.phpやcreate.blade.phpで使用してください。
--}}

<x-admin-layout>
    <div class="admin-form-container">
        {{-- フォームヘッダー --}}
        <div class="admin-form-header">
            <h1 class="admin-form-title">フォームタイトル</h1>
            <a href="{{ route('admin.example.index') }}" class="admin-form-back-link">
                <i class="fas fa-arrow-left mr-2"></i>一覧に戻る
            </a>
        </div>

        {{-- エラーメッセージ表示エリア --}}
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-md">
                <ul class="list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- フォーム開始 --}}
        <form method="POST" action="{{ route('admin.example.store') }}" enctype="multipart/form-data" class="admin-form-body">
            @csrf

            {{-- セクション1: 基本情報 --}}
            <div class="admin-form-section">
                <h2 class="admin-form-section-title">基本情報</h2>

                {{-- テキスト入力（必須） --}}
                <div class="admin-form-row">
                    <div class="admin-form-label-area">
                        <label for="name" class="admin-form-label">
                            名前
                            <span class="admin-form-label-required">*</span>
                        </label>
                        <p class="admin-form-label-description">必須項目です</p>
                    </div>
                    <div class="admin-form-input-area">
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            value="{{ old('name') }}"
                            class="admin-form-input @error('name') admin-form-input-error @enderror"
                            required
                        >
                        @error('name')
                            <p class="admin-form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- テキスト入力（任意） --}}
                <div class="admin-form-row">
                    <div class="admin-form-label-area">
                        <label for="email" class="admin-form-label">
                            メールアドレス
                            <span class="admin-form-label-optional">（任意）</span>
                        </label>
                    </div>
                    <div class="admin-form-input-area">
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            value="{{ old('email') }}"
                            class="admin-form-input @error('email') admin-form-input-error @enderror"
                        >
                        @error('email')
                            <p class="admin-form-error">{{ $message }}</p>
                        @enderror
                        <p class="admin-form-help">連絡先として使用されます</p>
                    </div>
                </div>

                {{-- テキストエリア --}}
                <div class="admin-form-row">
                    <div class="admin-form-label-area">
                        <label for="description" class="admin-form-label">
                            説明
                        </label>
                    </div>
                    <div class="admin-form-input-area">
                        <textarea 
                            id="description" 
                            name="description" 
                            class="admin-form-textarea @error('description') admin-form-input-error @enderror"
                            rows="5"
                        >{{ old('description') }}</textarea>
                        @error('description')
                            <p class="admin-form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- セレクトボックス --}}
                <div class="admin-form-row">
                    <div class="admin-form-label-area">
                        <label for="status" class="admin-form-label">
                            ステータス
                            <span class="admin-form-label-required">*</span>
                        </label>
                    </div>
                    <div class="admin-form-input-area">
                        <select 
                            id="status" 
                            name="status" 
                            class="admin-form-select @error('status') admin-form-input-error @enderror"
                            required
                        >
                            <option value="">選択してください</option>
                            <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>有効</option>
                            <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>無効</option>
                        </select>
                        @error('status')
                            <p class="admin-form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- チェックボックス --}}
                <div class="admin-form-row">
                    <div class="admin-form-label-area">
                        <label class="admin-form-label">
                            オプション
                        </label>
                    </div>
                    <div class="admin-form-input-area">
                        <div class="admin-form-checkbox-group">
                            <div class="admin-form-checkbox-item">
                                <input 
                                    type="checkbox" 
                                    id="option1" 
                                    name="options[]" 
                                    value="1"
                                    class="admin-form-checkbox"
                                    {{ in_array('1', old('options', [])) ? 'checked' : '' }}
                                >
                                <label for="option1" class="admin-form-checkbox-label">オプション1</label>
                            </div>
                            <div class="admin-form-checkbox-item">
                                <input 
                                    type="checkbox" 
                                    id="option2" 
                                    name="options[]" 
                                    value="2"
                                    class="admin-form-checkbox"
                                    {{ in_array('2', old('options', [])) ? 'checked' : '' }}
                                >
                                <label for="option2" class="admin-form-checkbox-label">オプション2</label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ラジオボタン --}}
                <div class="admin-form-row">
                    <div class="admin-form-label-area">
                        <label class="admin-form-label">
                            カテゴリ
                            <span class="admin-form-label-required">*</span>
                        </label>
                    </div>
                    <div class="admin-form-input-area">
                        <div class="admin-form-radio-group">
                            <div class="admin-form-radio-item">
                                <input 
                                    type="radio" 
                                    id="category1" 
                                    name="category" 
                                    value="1"
                                    class="admin-form-radio"
                                    {{ old('category') == '1' ? 'checked' : '' }}
                                >
                                <label for="category1" class="admin-form-radio-label">カテゴリ1</label>
                            </div>
                            <div class="admin-form-radio-item">
                                <input 
                                    type="radio" 
                                    id="category2" 
                                    name="category" 
                                    value="2"
                                    class="admin-form-radio"
                                    {{ old('category') == '2' ? 'checked' : '' }}
                                >
                                <label for="category2" class="admin-form-radio-label">カテゴリ2</label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ファイルアップロード --}}
                <div class="admin-form-row">
                    <div class="admin-form-label-area">
                        <label for="image" class="admin-form-label">
                            画像
                        </label>
                    </div>
                    <div class="admin-form-input-area">
                        <input 
                            type="file" 
                            id="image" 
                            name="image" 
                            accept="image/*"
                            class="admin-form-file @error('image') admin-form-input-error @enderror"
                        >
                        @error('image')
                            <p class="admin-form-error">{{ $message }}</p>
                        @enderror
                        @if(isset($item) && $item->image)
                            <img src="{{ Storage::url($item->image) }}" alt="プレビュー" class="admin-form-image-preview">
                        @endif
                    </div>
                </div>
            </div>

            {{-- セクション2: 詳細情報 --}}
            <div class="admin-form-section">
                <h2 class="admin-form-section-title">詳細情報</h2>

                {{-- インラインフォーム（横並び） --}}
                <div class="admin-form-row">
                    <div class="admin-form-label-area">
                        <label class="admin-form-label">
                            期間
                        </label>
                    </div>
                    <div class="admin-form-input-area">
                        <div class="admin-form-inline">
                            <div class="admin-form-inline-item">
                                <label for="start_date" class="admin-form-label">開始日</label>
                                <input 
                                    type="date" 
                                    id="start_date" 
                                    name="start_date" 
                                    value="{{ old('start_date') }}"
                                    class="admin-form-input"
                                >
                            </div>
                            <div class="admin-form-inline-item">
                                <label for="end_date" class="admin-form-label">終了日</label>
                                <input 
                                    type="date" 
                                    id="end_date" 
                                    name="end_date" 
                                    value="{{ old('end_date') }}"
                                    class="admin-form-input"
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- フォームフッター（ボタンエリア） --}}
            <div class="admin-form-footer">
                <a href="{{ route('admin.example.index') }}" class="admin-form-button-secondary">
                    キャンセル
                </a>
                <button type="submit" class="admin-form-button-primary">
                    <i class="fas fa-save mr-2"></i>保存
                </button>
            </div>
        </form>
    </div>
</x-admin-layout>





