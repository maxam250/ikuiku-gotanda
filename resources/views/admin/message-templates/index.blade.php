<x-admin-layout>
    <x-slot name="header">
            テンプレ管理
    </x-slot>
    <section>
        <div class="py-12">
            <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center mb-6">
                            <form action="{{ route('admin.message-templates.index') }}" method="GET" class="flex items-center gap-2">
                                <select name="type" class="form-select rounded-md shadow-sm w-40">
                                    <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>全て</option>
                                    <option value="member" {{ request('type') == 'member' ? 'selected' : '' }}>会員向け</option>
                                    <option value="cast" {{ request('type') == 'cast' ? 'selected' : '' }}>キャスト向け</option>
                                    <option value="common" {{ request('type') == 'common' ? 'selected' : '' }}>共通</option>
                                </select>
                                <button type="submit" class="btn btn-secondary btn-filter whitespace-nowrap">
                                    <i class="fas fa-filter"></i> 絞り込み
                                </button>
                            </form>
                            <a href="{{ route('admin.message-templates.create') }}" class="btn btn-primary whitespace-nowrap">
                                <i class="fas fa-plus"></i> 新規テンプレート作成
                            </a>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success mb-4">
                                {{ session('success') }}
                            </div>
                        @endif

                        <div class="overflow-x-auto">
                            <table class="min-w-full table table-hover">
                                <thead>
                                    <tr>
                                        <th>タイトル</th>
                                        <th>種別</th>
                                        <th>説明</th>
                                        <th>ステータス</th>
                                        <th>最終更新</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($templates as $template)
                                        <tr>
                                            <td class="align-middle">{{ $template->title }}</td>
                                            <td class="align-middle">
                                                @switch($template->type)
                                                    @case('member')
                                                        <span class="badge bg-primary">会員向け</span>
                                                        @break
                                                    @case('cast')
                                                        <span class="badge bg-info">キャスト向け</span>
                                                        @break
                                                    @case('common')
                                                        <span class="badge bg-success">共通</span>
                                                        @break
                                                @endswitch
                                            </td>
                                            <td class="align-middle">{{ $template->description }}</td>
                                            <td class="align-middle">
                                                @if($template->is_active)
                                                    <span class="badge bg-success">有効</span>
                                                @else
                                                    <span class="badge bg-secondary">無効</span>
                                                @endif
                                            </td>
                                            <td class="align-middle">{{ $template->updated_at->format('Y/m/d H:i') }}</td>
                                            <td class="align-middle">
                                                <div class="admin-btn-group">
                                                    <button type="button" class="admin-btn admin-btn-light admin-btn-sm preview-btn"
                                                            data-template-id="{{ $template->id }}">
                                                        <i class="fas fa-eye"></i>プレビュー
                                                    </button>
                                                    <a href="{{ route('admin.message-templates.edit', $template) }}"
                                                       class="admin-btn admin-btn-secondary admin-btn-sm">
                                                        <i class="fas fa-edit"></i>編集
                                                    </a>
                                                    <form action="{{ route('admin.message-templates.destroy', $template) }}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('このテンプレートを削除してもよろしいですか？');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="admin-btn admin-btn-warning admin-btn-sm">
                                                            <i class="fas fa-trash"></i>削除
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $templates->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- プレビューモーダル（Bootstrap 不使用） -->
        <div class="af-modal-overlay" id="previewModal" tabindex="-1" aria-labelledby="previewModalTitle" aria-hidden="true" role="dialog" aria-modal="true">
            <div class="af-modal">
                <div class="af-modal-header">
                    <h5 class="af-modal-title" id="previewModalTitle">テンプレートプレビュー</h5>
                    <button type="button" class="af-modal-close" data-af-modal-close aria-label="閉じる">&times;</button>
                </div>
                <div class="af-modal-body">
                    <div id="templateContent" class="template-preview" style="background:#f8f9fa;padding:1.25rem;border-radius:0.5rem;"></div>
                </div>
            </div>
        </div>

        @push('styles')
        <style>
        .btn-group {
            display: flex;
            gap: 0.5rem;
        }

        .btn-dark {
            background-color: #343a40;
            border-color: #343a40;
            color: white !important;
        }

        .btn-dark:hover {
            background-color: #23272b;
            border-color: #1d2124;
            color: white !important;
        }

        .btn-group .btn {
            border-radius: 0.25rem !important;
        }

        .template-preview {
            white-space: normal;
            word-wrap: break-word;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.8;
            font-size: 1rem;
        }

        #previewModal .af-modal {
            max-width: min(620px, 100%);
        }
        </style>
        @endpush

        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // プレビューボタンのクリックイベント
                document.querySelectorAll('.preview-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const templateId = this.dataset.templateId;
                        fetch(`/kanrigamen/message-templates/${templateId}/preview`)
                            .then(response => response.json())
                            .then(data => {
                                const content = data.content.replace(/\n/g, '<br>');
                                document.getElementById('templateContent').innerHTML = content;
                                if (window.AfModal) {
                                    window.AfModal.open('#previewModal');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                document.getElementById('templateContent').textContent = 'エラーが発生しました。';
                                if (window.AfModal) {
                                    window.AfModal.open('#previewModal');
                                }
                            });
                    });
                });
            });
        </script>
        @endpush
    </section>
</x-admin-layout>
