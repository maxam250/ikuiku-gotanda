@php
    $expiresAt = data_get($value, 'expires_at') ?? data_get($value, 'body');
    $label = data_get($value, 'label', 'カウントダウン');
    $target = null;
    if ($expiresAt) {
        try {
            $target = \Carbon\Carbon::parse($expiresAt);
        } catch (\Throwable $e) {
            $target = null;
        }
    }
@endphp

<div
    class="fanclub-timer border p-3 bg-gray-50"
    @if($target) data-timer-target="{{ $target->toIso8601String() }}" @endif
>
    <p class="text-xs text-gray-500">{{ $label }}</p>
    <p class="font-bold js-timer-text">
        @if($target)
            {{ $target->isPast() ? '終了しました' : '計算中...' }}
        @else
            日時未設定
        @endif
    </p>
</div>
