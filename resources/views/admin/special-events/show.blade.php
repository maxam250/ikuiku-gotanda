<x-admin-layout>
<x-slot name="header">
 特設イベント詳細
</x-slot>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">特設イベント詳細</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.special-events.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> 一覧に戻る
                        </a>
                        <a href="{{ route('admin.special-events.edit', $specialEvent) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> 編集
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="150">ID</th>
                                    <td>{{ $specialEvent->id }}</td>
                                </tr>
                                <tr>
                                    <th>イベント名</th>
                                    <td>{{ $specialEvent->title }}</td>
                                </tr>
                                <tr>
                                    <th>開催日</th>
                                    <td>{{ $specialEvent->event_date->format('Y年m月d日') }}</td>
                                </tr>
                                <tr>
                                    <th>時間</th>
                                    <td>{{ $specialEvent->start_time }} - {{ $specialEvent->end_time }}</td>
                                </tr>
                                <tr>
                                    <th>料金</th>
                                    <td>
                                        @if($specialEvent->fee)
                                            ¥{{ number_format($specialEvent->fee) }}
                                        @else
                                            <span class="text-muted">未設定</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>場所</th>
                                    <td>{{ $specialEvent->venue ?? '未設定' }}</td>
                                </tr>
                                <tr>
                                    <th>ステータス</th>
                                    <td>
                                        <span class="badge {{ $specialEvent->is_active ? 'badge-success' : 'badge-secondary' }}">
                                            {{ $specialEvent->is_active ? '有効' : '無効' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>タイマー表示</th>
                                    <td>
                                        @if($specialEvent->has_timer)
                                            <span class="badge badge-info">有効</span>
                                            @if($specialEvent->timer_start && $specialEvent->timer_end)
                                                <br><small class="text-muted">
                                                    {{ $specialEvent->timer_start->format('Y/m/d') }} - {{ $specialEvent->timer_end->format('Y/m/d') }}
                                                </small>
                                            @endif
                                        @else
                                            <span class="badge badge-secondary">無効</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>ソート順</th>
                                    <td>{{ $specialEvent->sort_order }}</td>
                                </tr>
                                <tr>
                                    <th>作成日</th>
                                    <td>{{ $specialEvent->created_at->format('Y年m月d日 H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>更新日</th>
                                    <td>{{ $specialEvent->updated_at->format('Y年m月d日 H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-4">
                            @if($specialEvent->main_image)
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">メイン画像</h5>
                                    </div>
                                    <div class="card-body">
                                        <img src="{{ asset('storage/' . $specialEvent->main_image) }}" alt="メイン画像" class="img-fluid">
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>内容</h5>
                            <div class="border p-3 bg-light">
                                {!! html_entity_decode($specialEvent->description, ENT_QUOTES | ENT_HTML5, 'UTF-8') !!}
                            </div>
                        </div>
                    </div>

                    @if($specialEvent->fee_note)
                        <div class="row mt-3">
                            <div class="col-12">
                                <h5>料金注意事項</h5>
                                <div class="border p-3 bg-light">
                                    {!! nl2br(e($specialEvent->fee_note)) !!}
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($specialEvent->access_info)
                        <div class="row mt-3">
                            <div class="col-12">
                                <h5>アクセス案内</h5>
                                <div class="border p-3 bg-light">
                                    {!! nl2br(e($specialEvent->access_info)) !!}
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($specialEvent->map_url)
                        <div class="row mt-3">
                            <div class="col-12">
                                <h5>Google Map</h5>
                                <a href="{{ $specialEvent->map_url }}" target="_blank" class="btn btn-outline-primary">
                                    <i class="fas fa-map-marker-alt"></i> 地図を開く
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($specialEvent->external_link)
                        <div class="row mt-3">
                            <div class="col-12">
                                <h5>外部リンク</h5>
                                <a href="{{ $specialEvent->external_link }}" target="_blank" class="btn btn-outline-primary">
                                    <i class="fas fa-external-link-alt"></i> リンクを開く
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($specialEvent->notes)
                        <div class="row mt-3">
                            <div class="col-12">
                                <h5>備考・注意事項</h5>
                                <div class="border p-3 bg-light">
                                    {!! nl2br(e($specialEvent->notes)) !!}
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>参加キャスト ({{ $specialEvent->items->count() }}名)</h5>
                            @if($specialEvent->items->count() > 0)
                                <div class="row">
                                    @foreach($specialEvent->items as $item)
                                        <div class="col-md-3 col-sm-6 mb-2">
                                            <div class="card">
                                                <div class="card-body p-2">
                                                    <h6 class="card-title mb-1">{{ $item->item_name }}</h6>
                                                    <small class="text-muted">ID: {{ $item->id }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-info">
                                    参加キャストが設定されていません。
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-admin-layout>
