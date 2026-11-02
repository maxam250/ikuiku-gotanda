
<x-admin-layout>


<x-slot name="header">
 ピックアップ
</x-slot>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 1rem; margin-bottom: 2rem; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="padding: 1rem; margin-bottom: 2rem; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px;">
            {{ session('error') }}
        </div>
    @endif

    <section class="onPickupArea">
        <h3 class="section-title active">ピックアップ表示中</h3>
        <ul class="list sortable-list">
            @foreach ($pickups as $pickup)
            <li class="box" data-pickup-id="{{ $pickup->id }}" id="pickup-{{ $pickup->id }}">
                <div class="img">
                    <img src="{{ $pickup->itemImage && $pickup->itemImage->filename
                        ? Storage::url($pickup->itemImage->filename)
                        : asset('images/dmy.jpg') }}"
                        alt="キャスト画像">
                </div>
                <div class="name">{{ $pickup->item->item_name }}</div>

                <!-- 削除ボタン -->
                <button class="admin-btn admin-btn-warning admin-btn-sm delete-btn" data-pickup-id="{{ $pickup->id }}">
                    <i class="fas fa-trash"></i>削除
                </button>
            </li>
            @endforeach
        </ul>
    </section>

    <section class="offPickupArea">
        <h3 class="section-title">ピックアップするキャストを選択</h3>

        {{-- 名前フィルタ --}}
        <form method="GET" action="{{ route('admin.pickups.index') }}" style="margin-bottom:1.5rem;display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap;">
            <input type="text" name="search" value="{{ $search }}" placeholder="名前で絞り込み" class="modern-input" style="width:200px;">
            <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm"><i class="fas fa-search"></i> 絞り込み</button>
            @if($search)<a href="{{ route('admin.pickups.index') }}" class="admin-btn admin-btn-sm">クリア</a>@endif
        </form>

        @if($non_pickups->total() > 0)
        <form action="{{ route('admin.pickups.bulkUpdate') }}" method="POST">
            @csrf
            <div style="margin:1rem 0;">
                {{ $non_pickups->links('vendor.pagination.admin-items') }}
            </div>
            <div class="btnArea" style="text-align: center; margin-bottom: 2rem;">
                <button type="submit" class="af-btn af-btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    一斉登録
                </button>
            </div>
            <ul class="list">
                @foreach ($non_pickups as $non_pickup)
                <li class="box">
                    <label>
                        <div class="img">
                            <img src="{{ $non_pickup->thumbnail && $non_pickup->thumbnail->filename
                                ? Storage::url($non_pickup->thumbnail->filename)
                                : asset('images/dmy.jpg') }}"
                                alt="キャスト画像">
                        </div>
                        <div class="name">{{ $non_pickup->item_name }}</div>

                        <!-- チェックボックス -->
                        <div class="apply">
                            <input type="checkbox" name="items[{{ $non_pickup->id }}][selected]" value="1" id="pu{{ $non_pickup->id }}">
                            <label class="checkboxs" for="pu{{ $non_pickup->id }}">追加</label>
                        </div>
                    </label>
                </li>
                @endforeach
            </ul>
            <div style="margin:1rem 0;">
                {{ $non_pickups->links('vendor.pagination.admin-items') }}
            </div>
            <div class="btnArea" style="text-align: center; margin-top: 2rem;">
                <button type="submit" class="af-btn af-btn-primary">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    一斉登録
                </button>
            </div>
        </form>
        @elseif($search)
        <p style="color:#6b7280;">「{{ $search }}」に一致するキャストはいません</p>
        @endif
    </section>


<!-- jQueryとSortable用のスクリプト -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script>

// 並び替え

$(function() {
    // 並び替え可能にする
    $(".sortable-list").sortable({
        delay: 0,
        distance: 1,
        scrollSpeed: 25,
        scrollSensitivity: 50,
        update: function(event, ui) {
            let pickupOrder = [];
            $(this).find('li').each(function(index) {
                let pickupId = $(this).data('pickup-id');
                pickupOrder.push({
                    id: pickupId,
                    sort: index + 1 // 並び順を1からスタートさせる
                });
            });

            // Ajaxで並び順をサーバーに送信
            $.ajax({
                url: "{{ route('admin.pickups.sort') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    pickupOrder: pickupOrder
                },
                success: function(response) {
                    if (!response || !response.success) {
                        alert('並び替えに失敗しました');
                    }
                },
                error: function() {
                    alert('エラーが発生しました');
                }
            });
        }
    });
});


// ピックアップから削除
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const pickupId = this.getAttribute('data-pickup-id');
            const pickupElement = document.getElementById('pickup-' + pickupId);

            if (confirm('本当に削除しますか？')) {
                fetch(`/kanrigamen/pickups/${pickupId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    if (!response.ok) {
                        const contentType = response.headers.get('content-type');
                        if (contentType && contentType.includes('application/json')) {
                            const text = await response.text();
                            const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
                            try {
                                const err = JSON.parse(cleanedText);
                                throw new Error(err.message || 'サーバーエラー');
                            } catch (e) {
                                if (e instanceof SyntaxError) {
                                    throw new Error('サーバーエラー: レスポンスの解析に失敗しました');
                                }
                                throw e;
                            }
                        }
                        throw new Error('サーバーエラーが発生しました');
                    }
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        const text = await response.text();
                        const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
                        try {
                            return JSON.parse(cleanedText);
                        } catch (e) {
                            console.error('JSON parse error:', e);
                            return { success: true, message: '削除が完了しました' };
                        }
                    }
                    const text = await response.text();
                    const cleanedText = text.replace(/^\uFEFF+/, '').trim(); // BOMを除去
                    try {
                        return JSON.parse(cleanedText);
                    } catch {
                        return { success: true, message: '削除が完了しました' };
                    }
                })
                .then(data => {
                    if (data && data.success) {
                        // フェードアウトアニメーションを追加
                        pickupElement.style.transition = 'opacity 0.3s';
                        pickupElement.style.opacity = '0';
                        setTimeout(() => {
                            pickupElement.remove();
                        }, 300);
                    } else {
                        alert(data ? (data.message || '削除に失敗しました') : 'エラーが発生しました: レスポンスが不正です');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert(error.message || 'エラーが発生しました');
                });
            }
        });
    });
});


</script>


</x-admin-layout>
