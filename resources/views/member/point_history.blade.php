@extends('layouts.app')

@section('breadcrumb_label', 'ポイント履歴')

@section('content')
<div class="titleArea">
    <h2>POINT</h2>
    <div class="title">ポイント履歴</div>
</div>

<section class="contentArea">
    @if($pointHistory->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>日付</th>
                        <th>種別</th>
                        <th class="text-end">ポイント</th>
                        <th>メモ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pointHistory as $point)
                    <tr>
                        <td class="text-nowrap">{{ $point->created_at->format('Y/m/d') }}</td>
                        <td>
                            <span class="badge {{ $point->is_add ? 'bg-success' : 'bg-secondary' }}">
                                {{ $point->type_label }}
                            </span>
                        </td>
                        <td class="text-end fw-bold {{ $point->is_add ? 'text-success' : 'text-secondary' }}">
                            {{ $point->point_display }}
                        </td>
                        <td>{{ $point->memo ?? '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $pointHistory->links() }}
        </div>
    @else
        <div class="text-center py-5 text-muted">
            <p>ポイント履歴はありません</p>
        </div>
    @endif
{{-- ポイント購入へのリンク --}}
{{--
    <div class="mt-3">
        <a href="{{ route('member.points.purchase') }}" class="btn btn-primary btn-sm">ポイントを購入する</a>
    </div>
--}}
</section>
@endsection
