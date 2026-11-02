
<x-item-layout>


<x-slot name="header">
キャスト一覧
</x-slot>


<section class="registerFormArea">
    <div class="add">
    <h3>キャスト登録</h3>
    <div class="hint">詳細は登録後に編集できます</div>

    <form method="post" action="{{ route('item.items.store') }}">
        @csrf
        <div>
            <input type="text" name="item_name" id="name" class="form-control" required placeholder="キャスト名">
            <button type="submit" class="af-btn af-btn-primary">登録</button>
        </div>

    </form>
    </div>
</section>

<section id="ListArea">
<ul class="list">
    @foreach ($items as $item)
    <li class="box
    @if($item->item_active == 1)
     active
    @else
     passive
    @endif" data-id="{{ $item->id }}">
    <div class="img">
        <a href="{{ route('item.items.edit', $item) }}">
    @if ($item->thumbnail)
    <img src="{{ asset('uploads/' . $item->thumbnail->filename)}}" alt="">
    @else
    <img src="{{ asset('images/dmy.jpg') }}" alt="">
    @endif </a>
    </div>
        <div class="name"><a href="{{ route('item.items.edit', $item) }}">{{ $item->item_name }}({{ $item->item_age }})</a></div>

    </li>
    @endforeach
</ul>


</section>



<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var el = document.querySelector('.list');
        var sortable = new Sortable(el, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function (/**Event*/evt) {
                var itemEl = evt.item;  // dragged HTMLElement
                updateOrder();
            }
        });

    function updateOrder() {
        var order = [];
        document.querySelectorAll('.list .box').forEach(function(item, index) {
            order.push({ id: item.dataset.id, item_sort: index + 1 });
        });

        fetch('{{ route("item.items.sort") }}', {

            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ order: order })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Order updated', data);
        })
        .catch(error => console.error('Error:', error));
    }
});
</script>

</x-item-layout>
