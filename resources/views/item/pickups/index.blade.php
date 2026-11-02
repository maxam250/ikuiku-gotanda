
<x-item-layout>


<x-slot name="header">
 ピックアップ
</x-slot>

<section class="onPickup">

    <h3>ピックアップ表示中</h3>
    <ul class="list sortable-list">
        @foreach ($pickups as $pickup)


        <li class="box" data-pickup-id="{{ $pickup->id }}" id="pickup-{{ $pickup->id }}">
            <div class="img">
                <img src="{{ $pickup->itemImage && $pickup->itemImage->filename 
                ? asset('uploads/' . $pickup->itemImage->filename) 
                : asset('images/dmy.jpg') }}" 
                alt="キャスト画像">
            </div>
            <div class="name">{{ $pickup->item->item_name }}</div>

            <!-- 削除ボタン -->
            <button class="btn delete-btn" data-pickup-id="{{ $pickup->id }}">削除</button>
        </li>
        @endforeach
    </ul>
</section>


<section class="offPickup">
@if (count($non_pickups))
    <h3>ピックアップするキャストを選択</h3>
    <form action="{{ route('item.pickups.bulkUpdate') }}" method="POST">
        @csrf
    
    <button type="submit" class="btn size_m">一斉登録</button>    
    <ul class="list">
    @foreach ($non_pickups as $non_pickup)
    <li class="box">
        <label>
            <div class="img">                
                <img src="{{ $non_pickup->itemImage && $non_pickup->itemImage->filename 
                ? asset('uploads/' . $non_pickup->itemImage->filename) 
                : asset('images/dmy.jpg') }}" 
                alt="キャスト画像">
            </div>
            <div class="name">{{ $non_pickup->item_name }}</div>

            <!-- チェックボックス --> 
            <input type="checkbox" name="items[{{ $non_pickup->id }}][selected]" value="1" id="pu{{ $non_pickup->id }}"><label class="checkboxs" for="pu{{ $non_pickup->id }}"> 追加</label>                    
        </label>


    </li>
    @endforeach
    </ul>
    <button type="submit" class="btn size_m">一斉登録</button>
    </form>
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
                url: "{{ route('item.pickups.sort') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    pickupOrder: pickupOrder
                },
                success: function(response) {
                    if(response.success) {
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


// ピックアップから削除
$(document).ready(function() {
    // 削除ボタンがクリックされたらAjaxで削除リクエストを送信
    $('.delete-btn').on('click', function() {
        const pickupId = $(this).data('pickup-id');
        const pickupElement = $('#pickup-' + pickupId);

        if (confirm('本当に削除しますか？')) {
            $.ajax({
                url: '/item/pickups/' + pickupId,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        // 削除されたリスト項目をフェードアウトで消す
                        pickupElement.fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        alert('削除に失敗しました');
                    }
                },
                error: function() {
                    alert('エラーが発生しました');
                }
            });
        }
    });
});


</script>





</x-item-layout>
