<x-admin-layout>

<x-slot name="header">
新人情報
</x-slot>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 1rem; margin-bottom: 1rem; background: #d4edda; color: #155724; border: 1px solid #c3e6cb; border-radius: 4px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-info" style="padding: 1rem; margin-bottom: 1rem; background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; border-radius: 4px;">
            <i class="fas fa-info-circle"></i> {{ session('info') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger" style="padding: 1rem; margin-bottom: 1rem; background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 4px;">
            {{ session('error') }}
        </div>
    @endif

{{-- 一括処理 --}}
<section class="modern-register-form">
    <div class="form-container">
        <h3>一括処理（登録日が古い新人情報の削除）</h3>
        <p class="form-hint" style="color:#dc2626;margin-top:0.5rem;">
            <i class="fas fa-exclamation-circle"></i> 指定日より以前に登録された新人情報を一括削除できます。キャスト自体は削除されません。
        </p>
        <form method="POST" action="{{ route('admin.newfaces.bulkAction') }}" onsubmit="return confirm(document.querySelector('[name=before_date]').value.replace(/-/g,'/') + ' より以前の新人情報を削除します。\n\nよろしいですか？')" class="inline-form">
            @csrf
            <div class="form-row" style="flex-wrap:wrap;align-items:center;gap:0.6rem;">
                <input type="date" name="before_date" class="modern-input" required style="width:160px;flex:none;">
                <span style="font-size:0.875rem;white-space:nowrap;">より以前のものを</span>
                <input type="hidden" name="action" value="remove">
                <button type="submit" class="admin-btn admin-btn-warning" style="white-space:nowrap;">
                    <i class="fas fa-trash"></i> 新人情報から削除
                </button>
            </div>
        </form>
    </div>
</section>

{{-- 登録中 --}}
<section class="onPickupArea">
    <h3 class="section-title active">新人情報 表示中</h3>
    <ul class="list sortable-list">
        @foreach ($newfaces as $newface)
        <li class="box" data-newface-id="{{ $newface->id }}" id="newface-{{ $newface->id }}">
            <div class="img">
                <img src="{{ $newface->itemImage && $newface->itemImage->filename
                    ? Storage::url($newface->itemImage->filename)
                    : asset('images/dmy.jpg') }}"
                    alt="キャスト画像">
            </div>
            <div class="name">{{ $newface->item->item_name ?? '不明' }}</div>
            <div style="font-size:0.75rem;color:#6b7280;">登録日: {{ \Carbon\Carbon::parse($newface->date)->format('Y/m/d') }}</div>

            <button class="admin-btn admin-btn-warning admin-btn-sm delete-btn" data-newface-id="{{ $newface->id }}">
                <i class="fas fa-trash"></i>削除
            </button>
        </li>
        @endforeach
    </ul>
</section>

{{-- 未登録キャスト --}}
<section class="offPickupArea">
    <h3 class="section-title">新人情報に追加するキャストを選択</h3>

    {{-- 名前フィルタ --}}
    <form method="GET" action="{{ route('admin.newfaces.index') }}" style="margin-bottom:1.5rem;display:flex;gap:0.5rem;align-items:center;flex-wrap:wrap;">
        <input type="text" name="search" value="{{ $search }}" placeholder="名前で絞り込み" class="modern-input" style="width:200px;">
        <button type="submit" class="admin-btn admin-btn-primary admin-btn-sm"><i class="fas fa-search"></i> 絞り込み</button>
        @if($search)<a href="{{ route('admin.newfaces.index') }}" class="admin-btn admin-btn-sm">クリア</a>@endif
    </form>

    @if($non_newfaces->total() > 0)
    <form action="{{ route('admin.newfaces.bulkUpdate') }}" method="POST">
        @csrf
        <div style="margin:1rem 0;">
            {{ $non_newfaces->links('vendor.pagination.admin-items') }}
        </div>
        <div class="btnArea" style="text-align: center; margin-bottom: 2rem;">
            <button type="submit" class="af-btn af-btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                一斉登録
            </button>
        </div>
        <ul class="list">
            @foreach ($non_newfaces as $non_newface)
            <li class="box">
                <label>
                    <div class="img">
                        <img src="{{ $non_newface->thumbnail && $non_newface->thumbnail->filename
                            ? Storage::url($non_newface->thumbnail->filename)
                            : asset('images/dmy.jpg') }}"
                            alt="キャスト画像">
                    </div>
                    <div class="name">{{ $non_newface->item_name }}</div>
                    <div class="apply">
                        <input type="checkbox" name="items[{{ $non_newface->id }}][selected]" value="1" id="nf{{ $non_newface->id }}">
                        <label class="checkboxs" for="nf{{ $non_newface->id }}">追加</label>
                    </div>
                </label>
            </li>
            @endforeach
        </ul>
        <div style="margin:1rem 0;">
            {{ $non_newfaces->links('vendor.pagination.admin-items') }}
        </div>
        <div class="btnArea" style="text-align: center; margin-top: 2rem;">
            <button type="submit" class="af-btn af-btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                一斉登録
            </button>
        </div>
    </form>
    @elseif($search)
    <p style="color:#6b7280;">「{{ $search }}」に一致するキャストはいません</p>
    @endif
</section>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script>
// 並び替え
$(function() {
    $(".sortable-list").sortable({
        update: function(event, ui) {
            let newfaceOrder = [];
            $(this).find('li').each(function(index) {
                let newfaceId = $(this).data('newface-id');
                newfaceOrder.push({ id: newfaceId, sort: index + 1 });
            });

            $.ajax({
                url: "{{ route('admin.newfaces.sort') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    newfaceOrder: newfaceOrder
                },
                success: function(response) {
                    if (response.success) {
                        alert('並び替えが完了しました');
                    } else {
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

// 削除
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.delete-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const newfaceId = this.getAttribute('data-newface-id');
            const newfaceElement = document.getElementById('newface-' + newfaceId);

            if (confirm('新人情報から削除しますか？（キャスト自体は削除されません）')) {
                fetch(`/kanrigamen/newfaces/${newfaceId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(async response => {
                    const text = await response.text();
                    const cleanedText = text.replace(/^﻿+/, '').trim();
                    try {
                        return JSON.parse(cleanedText);
                    } catch {
                        return { success: response.ok };
                    }
                })
                .then(data => {
                    if (data && data.success) {
                        newfaceElement.style.transition = 'opacity 0.3s';
                        newfaceElement.style.opacity = '0';
                        setTimeout(() => { newfaceElement.remove(); }, 300);
                    } else {
                        alert(data ? (data.message || '削除に失敗しました') : 'エラーが発生しました');
                    }
                })
                .catch(error => {
                    alert(error.message || 'エラーが発生しました');
                });
            }
        });
    });
});
</script>

</x-admin-layout>
