
<x-item-layout>


<x-slot name="header">
        キャスト管理画面
</x-slot>


<section id="castAddArea">
    <h3>キャスト登録</h3>
    <form method="post" action="{{ route('item.casts.store') }}">
        @csrf
        <div class="form-group">
            <label for="name">キャスト名</label>
            <input type="text" name="cast_name" id="name" class="form-control" required>
            <button type="submit" class="btn btn-primary">登録</button>
        </div>

    </form>
</section>

<section id="castListArea">
<ul class="list">
    @foreach ($casts as $cast)
    <li class="box" style="background:
    @if($cast->cast_active == 1)
    #FDF;
    @else
    #ccc;
    @endif" data-id="{{ $cast->id }}">
    <div class="img">
        <a href="{{ route('item.casts.edit', $cast) }}">
    @if ($cast->thumbnail)
    <img src="{{ asset('uploads/' . $cast->thumbnail->filename)}}" alt="">
    @else
    <img src="{{ asset('images/dmy.jpg') }}" alt="">
    @endif </a>
    </div>
        <div class="name"><a href="{{ route('item.casts.edit', $cast) }}">{{ $cast->cast_name }}({{ $cast->cast_age }})</a></div>
        {{ $cast->cast_active ? 'Active' : 'Inactive' }}<br>
        sort:{{ $cast->cast_sort }}
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
            order.push({ id: item.dataset.id, cast_sort: index + 1 });
        });

        fetch('{{ route("item.casts.sort") }}', {

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
