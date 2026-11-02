<x-admin-layout>

    <x-slot name="header">
    コース新規作成
    </x-slot>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>コース新規作成</h1>
                <a href="{{ route('admin.course_fees.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> 一覧に戻る
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.course_fees.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">基本情報</h5>
                            </div>
                            <div class="card-body">
                                <!-- カテゴリ -->
                                <div class="mb-3">
                                    <label for="course_id" class="form-label">カテゴリ <span class="text-danger">*</span></label>
                                    <select class="form-control" id="course_id" name="course_id" required>
                                        <option value="" disabled selected>カテゴリを選択してください</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                {{ $course->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- コース(表示名) -->
                                <div class="mb-3">
                                    <label for="course" class="form-label">コース(表示名)</label>
                                    <input type="text" class="form-control" id="course" name="course"
                                           value="{{ old('course') }}">
                                </div>

                                <!-- コースの時間(分) -->
                                <div class="mb-3">
                                    <label for="course_time" class="form-label">コースの時間(分)</label>
                                    <div class="input-group">
                                        <input type="text" id="course_time" name="course_time"
                                               value="{{ old('course_time') }}" placeholder="60" style="width:5rem;">
                                        <span class="input-group-text">分</span>
                                    </div>
                                </div>

                                <!-- 料金 -->
                                <div class="mb-3">
                                    <label for="fee" class="form-label">料金 <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" style="width:5rem;" id="fee" name="fee"
                                               value="{{ old('fee') }}" required>
                                        <span class="input-group-text">円</span>
                                    </div>
                                </div>

                                <!-- 説明文 -->
                                <div class="mb-3">
                                    <label for="text" class="form-label">説明文</label>
                                    <textarea class="form-control" id="text" name="text"
                                              rows="3">{{ old('text') }}</textarea>
                                    <div class="form-text">管理画面でのメモ用途です</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- 操作ボタン -->
                        <div class="card">
                            <div class="card-body">
                                <button type="submit" class="af-btn af-btn-primary af-btn-full">
                                    <i class="fas fa-save"></i> 作成する
                                </button>
                                <a href="{{ route('admin.course_fees.index') }}" class="btn btn-secondary w-100">
                                    <i class="fas fa-times"></i> キャンセル
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
</x-admin-layout>
