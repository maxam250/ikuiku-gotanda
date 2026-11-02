<x-admin-layout>

    <x-slot name="header">
    プレミアランク編集
    </x-slot>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1>プレミアランク編集</h1>
                <a href="{{ route('admin.premium-ranks.index') }}" class="btn btn-secondary">
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

            <form action="{{ route('admin.premium-ranks.update', $premiumRank->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">基本情報</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="name" class="form-label">ランク名 <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="{{ old('name', $premiumRank->name) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="fee" class="form-label">料金表</label>
                                    <textarea class="form-control" id="fee" name="fee" rows="5"
                                              style="resize:vertical;">{{ old('fee', $premiumRank->fee) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="fee_sub" class="form-label">旧料金表（タイマー時に使用）</label>
                                    <textarea class="form-control" id="fee_sub" name="fee_sub" rows="5"
                                              style="resize:vertical;">{{ old('fee_sub', $premiumRank->fee_sub) }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="note" class="form-label">メモ</label>
                                    <input type="text" class="form-control" id="note" name="note"
                                           value="{{ old('note', $premiumRank->note) }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <button type="submit" class="af-btn af-btn-primary af-btn-full">
                                    <i class="fas fa-save"></i> 更新する
                                </button>
                                <a href="{{ route('admin.premium-ranks.index') }}" class="btn btn-secondary w-100 mt-2">
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
