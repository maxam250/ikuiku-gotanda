<x-admin-layout>

    <x-slot name="header">
    特設イベント管理
    </x-slot>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('admin.special-events.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> 新規作成
                </a>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>タイトル</th>
                                    <th>開催日</th>
                                    <th>開始時間</th>
                                    <th>終了時間</th>
                                    <th>公開状態</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($specialEvents as $specialEvent)
                                    <tr>
                                        <td>{{ $specialEvent->id }}</td>
                                        <td>{{ $specialEvent->title }}</td>
                                        <td>{{ $specialEvent->event_date->format('Y/m/d') }}</td>
                                        <td>{{ sprintf('%02d:%02d', $specialEvent->start_hour, $specialEvent->start_minute) }}</td>
                                        <td>{{ sprintf('%02d:%02d', $specialEvent->end_hour, $specialEvent->end_minute) }}</td>
                                        <td>
                                            <span class="badge {{ $specialEvent->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $specialEvent->is_active ? '公開' : '非公開' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.special-events.edit', $specialEvent) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> 編集
                                            </a>
                                            <form action="{{ route('admin.special-events.destroy', $specialEvent) }}" method="POST" class="d-inline" onsubmit="return confirm('本当に削除しますか？');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i> 削除
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">特設イベントがありません</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $specialEvents->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</x-admin-layout>
