<x-admin-layout>

<x-slot name="header">
予約詳細
</x-slot>

<div class="back">
    <a href="{{ route('admin.reservations.index') }}" class="btn-primary">戻る</a>
    <a href="{{ route('admin.reservations.edit', $reservation->id) }}" class="btn btn-primary">編集</a>
</div>

<section class="MessageArea">
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
</section>

<section>
    <div class="form-table">
        <div class="form-group">
            <label class="form-label">予約ID</label>
            <div class="form-value">{{ $reservation->id }}</div>
        </div>
        
        <div class="form-group">
            <label class="form-label">ステータス</label>
            <div class="form-value">
                @switch($reservation->flag)
                    @case(0) 未設定 @break
                    @case(1) 確認中 @break
                    @case(2) 確定 @break
                    @case(3) 保留 @break
                    @case(4) キャンセル @break
                    @default 未設定
                @endswitch
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">予約種別</label>
            <div class="form-value">
                {{ $reservation->kind == 1 ? '通常予約' : 'リクエスト予約' }}
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">日時</label>
            <div class="form-value">
                {{ $reservation->date->format('Y年m月d日') }} {{ $reservation->time_start }}
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">コース</label>
            <div class="form-value">
                {{ $reservation->course ? $reservation->course->title : '未設定' }}
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">時間</label>
            <div class="form-value">
                {{ $reservation->course_time }}分
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">キャスト</label>
            <div class="form-value">
                {{ $reservation->item ? $reservation->item->item_name : $reservation->item_name }}
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">お客様名</label>
            <div class="form-value">
                {{ $reservation->name }}
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">メールアドレス</label>
            <div class="form-value">
                {{ $reservation->email }}
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">電話番号</label>
            <div class="form-value">
                {{ $reservation->tel }}
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">料金</label>
            <div class="form-value">
                コース料金: {{ number_format($reservation->fee_course) }}円<br>
                オプション料金: {{ number_format($reservation->fee_option) }}円<br>
                調整金: {{ number_format($reservation->fee_adjust) }}円<br>
                マイオプション料金: {{ number_format($reservation->fee_myoption) }}円<br>
                マイコスチューム料金: {{ number_format($reservation->fee_mycostume) }}円<br>
                <strong>合計: {{ number_format($reservation->fee_total) }}円</strong>
            </div>
        </div>
        
        @if($reservation->comment)
        <div class="form-group">
            <label class="form-label">備考</label>
            <div class="form-value">
                {{ $reservation->comment }}
            </div>
        </div>
        @endif
        
        @if($reservation->memo)
        <div class="form-group">
            <label class="form-label">メモ</label>
            <div class="form-value">
                {{ $reservation->memo }}
            </div>
        </div>
        @endif
    </div>
</section>

</x-admin-layout> 