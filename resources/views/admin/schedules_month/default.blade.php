
<x-admin-layout>


<x-slot name="header">
月間出勤
</x-slot>


<section id="itemList">
   {{-- <ul>
       @foreach($items as $list)
       <li>
           <a href="{{ route('admin.schedules_month.index', ['itemId' => $list->id, 'year' => $currentYear, 'month' => $currentMonth]) }}">{{ $list->item_name }}</a>
       </li>
       @endforeach
   </ul> --}}

       <!-- キャストのリストを表示 -->
       <section class="jumpMenu">
        <div class="jumpMenu-wrap">
            <input type="text" id="castNameSearch" placeholder="名前で絞り込み" class="cast-name-search">
            <form name="jumpForm">
                <div class="select-wrap">
                <select name="jumpMenu" id="jumpMenuSelect" onchange="jumpToPage(this)">
                    <option value="">編集するキャストを選択してください</option>
                    @foreach ($item_lists as $item_list)
                        <option value="{{ route('admin.schedules_month.index', ['itemId' => $item_list->id]) }}"
                            {{ request('itemId') == $item_list->id ? 'selected' : '' }}
                            data-name="{{ $item_list->item_name }}">
                            {{ $item_list->item_name }}
                        </option>
                    @endforeach
                </select>
                </div>
            </form>
        </div>
    </section>

       <script>
           function jumpToPage(selectObject) {
               if (selectObject.value != "") {
                   window.location.href = selectObject.value;
               }
           }
           document.getElementById('castNameSearch').addEventListener('input', function () {
               var keyword = this.value.trim().toLowerCase();
               var select  = document.getElementById('jumpMenuSelect');
               Array.from(select.options).forEach(function (opt) {
                   if (opt.value === '') return; // プレースホルダーは常に表示
                   opt.hidden = keyword !== '' && !opt.dataset.name.toLowerCase().includes(keyword);
               });
           });
       </script>
   </section>




</x-admin-layout>
