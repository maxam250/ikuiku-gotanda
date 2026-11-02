<x-admin-layout>

<x-slot name="header">
プレミアランク
</x-slot>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<section id="premiumRankAddArea" class="modern-register-form">
    <div class="form-container">
        <h3>プレミアランク登録</h3>
        <form method="post" action="{{ route('admin.premium-ranks.store') }}" class="inline-form">
            @csrf
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="form-row">
                <input type="text" name="name" class="modern-input" required placeholder="ランク名">
                <input type="text" name="note" class="modern-input" placeholder="予備用メモ">
                <button type="submit" class="admin-btn admin-btn-submit">
                    <i class="fas fa-plus"></i>登録
                </button>
            </div>
            <div class="form-row" style="margin-top:8px;">
                <textarea name="fee" class="modern-input" rows="3" placeholder="料金表メモ" style="width:100%;resize:vertical;"></textarea>
            </div>
            <div class="form-row" style="margin-top:8px;">
                <textarea name="fee_sub" class="modern-input" rows="3" placeholder="旧料金表メモ" style="width:100%;resize:vertical;"></textarea>
            </div>
        </form>
    </div>
</section>

<section>
    <table class="listTbl">
        <thead>
            <tr>
                <th>並替</th>
                <th>ランク名</th>
                <th>料金表</th>
                <th>旧料金表（タイマー時に使用）</th>
                <th>メモ</th>
                <th>アクション</th>
            </tr>
        </thead>
        <tbody id="sortable">
            @foreach ($premiumRanks as $premiumRank)
                <tr data-id="{{ $premiumRank->id }}">
                    <td class="handle center">
                        <span class="sort-handle">
                            <span></span><span></span><span></span>
                        </span>
                    </td>
                    <td>{{ $premiumRank->name }}</td>
                    <td style="white-space:pre-wrap;max-width:300px;">{{ $premiumRank->fee }}</td>
                    <td style="white-space:pre-wrap;max-width:300px;">{{ $premiumRank->fee_sub }}</td>
                    <td>{{ $premiumRank->note }}</td>
                    <td class="action">
                        <div class="admin-btn-group">
                            <a href="{{ route('admin.premium-ranks.edit', $premiumRank) }}" class="admin-btn admin-btn-secondary admin-btn-sm">
                                <i class="fas fa-edit"></i>編集
                            </a>
                            <form action="{{ route('admin.premium-ranks.destroy', $premiumRank) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="admin-btn admin-btn-warning admin-btn-sm" onclick="return confirm('本当に削除しますか？')">
                                    <i class="fas fa-trash"></i>削除
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        $(function() {
            $('#sortable').sortable({
                handle: '.sort-handle',
                update: function(event, ui) {
                    let order = [];
                    $('#sortable tr').each(function(index, element) {
                        order.push({
                            id: $(this).data('id'),
                            position: index + 1
                        });
                    });

                    $.ajax({
                        type: "POST",
                        url: "{{ route('admin.premium-ranks.sort') }}",
                        data: {
                            order: order,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            alert('並び替えが保存されました');
                        },
                        error: function(xhr) {
                            alert('並び替えの保存に失敗しました');
                        }
                    });
                }
            });
        });
    </script>
</section>

</x-admin-layout>
