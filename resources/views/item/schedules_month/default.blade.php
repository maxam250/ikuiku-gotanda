
<x-item-layout>


<x-slot name="header">
月間出勤
</x-slot>


<section id="itemList">
   {{-- <ul>
       @foreach($items as $list)
       <li>
           <a href="{{ route('item.schedules_month.index', ['itemId' => $list->id, 'year' => $currentYear, 'month' => $currentMonth]) }}">{{ $list->item_name }}</a>
       </li>
       @endforeach
   </ul> --}}

       <!-- キャストのリストを表示 -->
       <section class="jumpMenu">
        <form name="jumpForm">
            <select name="jumpMenu" onchange="jumpToPage(this)">
                <option value="">編集するキャストを選択してください</option>
                @foreach ($item_lists as $item_list)
                    <option value="{{ route('item.schedules_month.index', ['itemId' => $item_list->id]) }}"
                        {{ request('itemId') == $item_list->id ? 'selected' : '' }}>
                        {{ $item_list->item_name }}
                    </option>
                @endforeach
            </select>
        </form>
    </section>

       <script>
           function jumpToPage(selectObject) {
               if (selectObject.value != "") {
                   window.location.href = selectObject.value;
               }
           }
       </script>
   </section>




</x-item-layout>
