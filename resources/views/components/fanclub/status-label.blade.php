@props([
    'value',
    /** membership | fanclub */
    'kind' => 'membership',
    /** option など HTML を含められない箇所では true */
    'plain' => false,
])
@php
    $membershipLabels = [
        'active' => '入会中',
        'paused' => '一時停止',
        'canceled' => '退会済み',
    ];
    $fanclubLabels = [
        'active' => '公開中',
        'paused' => '一時停止',
        'canceled' => '終了',
    ];
    $labels = $kind === 'fanclub' ? $fanclubLabels : $membershipLabels;
    $key = $value !== null && $value !== '' ? (string) $value : null;
    $label = $key !== null && isset($labels[$key]) ? $labels[$key] : ($key ?? '—');
@endphp
@if($plain)
    {{ $label }}
@else
    <span @class([
        'member-tag',
        'member-tag--active' => $key === 'active',
        'member-tag--paused' => $key === 'paused',
        'member-tag--canceled' => $key === 'canceled',
    ])>{{ $label }}</span>
@endif
