<x-item-layout>


<x-slot name="header">
プロフィール編集
</x-slot>


<section class="MessageArea">
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif


    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</section>

<section id="imageList">
    <div class="mb-2">
        写真の更新はスタッフまでご連絡ください。
    </div>
<!-- 画像リスト -->
<ul class="list">
    @foreach ($images as $image)
    <li class="box" id="image-{{ $image->id }}">
        <div class="img">
            <img src="{{ asset('uploads/' . $image->filename) }}" alt="Image">
        </div>
    </li>
    @endforeach
</ul>
</section>



<div class="tab-wrap">



<input id="tab-info" type="radio" name="tabs" class="tab-switch" checked>
<label class="tab-label" for="tab-info">キャスト情報</label>

<section id="itemInfoArea" class="editSection tab-content">

    <table>
    <form method="POST" action="{{ route('item.items.update', $item) }}">
        @csrf
        @method('patch')

        <!-- <tr>
            <th><label for="item_active">公開:</label></th>
            <td>
                <label><input type="checkbox" id="item_active" name="item_active" value="1" {{ $item->item_active ? 'checked' : '' }}>&emsp;チェックを入れると公開されます</label>
            </td>
        </tr> -->
        <!-- <tr>
            <th><label for="item_cate">カテゴリ:</label></th>
            <td class="form_parts">
                <input type="radio" name="item_cate" id="item_cate0" value="0" {{ $item->item_cate == 0 ? 'checked' : '' }}>&emsp;<label class="radio" for="item_cate0">ノーマル</label>
                <input type="radio" name="item_cate" id="item_cate1" value="1" {{ $item->item_cate == 1 ? 'checked' : '' }}>&emsp;<label class="radio" for="item_cate1">プレミア</label>
                <input type="radio" name="item_cate" id="item_cate2" value="2" {{ $item->item_cate == 2 ? 'checked' : '' }}>&emsp;<label class="radio" for="item_cate2">プラチナ</label>
                <input type="radio" name="item_cate" id="item_cate3" value="3" {{ $item->item_cate == 3 ? 'checked' : '' }}>&emsp;<label class="radio" for="item_cate3">バナー</label>
            </td>
        </tr> -->
        <tr>
            <th><label for="item_name">名前:</label></th>
            <td>{{ $item->item_name }}
                <input type="hidden" name="item_name" value="{{ $item->item_name }}" required></td>
        </tr>
        <tr>
            <th>年齢</th>
            <td>
                <select name="item_age">
                    <option value="0"
                    @if( $item->item_age == 0 )
                     selected
                     @endif
                     > 未設定 </option>
                    @for ($a = 18; $a <= 40; $a++)
                        <option value="{{ $a }}"
                        @if( $a == $item->item_age )
                         selected
                         @endif
                         >{{ $a }}</option>
                    @endfor
                    </select>
        </td>
        </tr>
        <tr>
            <th>サイズ</th>
            <td>
            @php
                //初期値の設定
                $df_height = $item->item_height ?? 170;
                $df_b = $item->item_b ?? 85;
                $df_w = $item->item_w ?? 58;
                $df_h = $item->item_h ?? 85;
                $df_s = $item->item_shoes ?? 25;
            @endphp

                T <select name="item_height">
                @for ($t = 135; $t <= 190; $t++)
                    <option value="{{ $t }}"
                    @if( $t == $df_height )
                     selected
                    @endif
                     >{{ $t }}</option>
                @endfor
                </select>&nbsp;
                B <select name="item_b">
                @for ($b = 70; $b <= 100; $b++)
                    <option value="{{ $b }}"
                    @if( $b == $df_b )
                     selected
                    @endif
                     >{{ $b }}</option>
                @endfor
                </select>&nbsp;
                @php
                $cups = range('A', 'K');
                @endphp
                CUP <select name="item_cup">
                    @foreach ($cups as $cup)
                        <option value="{{ $cup }}"
                        @if( $cup == $item->item_cup )
                         selected
                         @endif
                         >{{ $cup }}</option>
                    @endforeach
                 </select>&nbsp;

                W <select name="item_w">
                @for ($w = 50; $w <= 90; $w++)
                    <option value="{{ $w }}"
                    @if( $w == $df_w )
                     selected
                    @endif
                     >{{ $w }}</option>
                @endfor
                </select>&nbsp;
                H <select name="item_h">
                @for ($h = 70; $h <= 100; $h++)
                    <option value="{{ $h }}"
                    @if( $h == $df_h )
                     selected
                    @endif
                     >{{ $h }}</option>
                @endfor
                </select>&nbsp;
                靴 <select name="item_shoes">
                @for ($s = 22; $s <= 28; $s++)
                <option value="{{ $s }}"
                @if( $s == $df_s )
                 selected
                @endif
                 >{{ $s }}</option>
                 <option value="{{ $s }}.5"
                 @if( ($s.".5") == $item->item_shoes )
                  selected
                  @endif
                  >{{ $s }}.5</option>
                @endfor
                </select>&nbsp;
            </td>
        </tr>
        <tr>
            <th>コメント</th>
            <td>
                <textarea  name="item_comment">{{ old('item_comment', $item->item_comment) }}</textarea></td>
        </tr>
        {{--
        <tr>
            <th>コメント2</th>
            <td>
                <textarea  name="item_comment2">{{ old('item_comment2', $item->item_comment2) }}</textarea></td>
        </tr>
        --}}
        <tr>
            <th>ブログ名</th>
            <td>
                <input type="text" name="item_blogname" value="{{ old('item_blogname', $item->item_blogname) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>ブログURL</th>
            <td>
                <input type="text" name="item_blog" value="{{ old('item_blog', $item->item_blog) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>X(旧Twitter)</th>
            <td>
                <input type="text"  name="item_twitter" value="{{ old('item_twitter', $item->item_twitter) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>その他URL</th>
            <td>
                <input type="text" name="item_url" value="{{ old('item_url', $item->item_url) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>インスタグラム</th>
            <td>
                <input type="text" name="item_instagram" value="{{ old('item_instagram', $item->item_instagram) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>TikTok</th>
            <td>
                <input type="text" name="item_tiktok" value="{{ old('item_tiktok', $item->item_tiktok) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>YouTube</th>
            <td>
                <input type="text" name="item_youtube" value="{{ old('item_youtube', $item->item_youtube) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>リアルタイムコメント</th>
            <td>
                <textarea  name="item_p_comment">{{ old('item_p_comment', $item->item_p_comment) }}</textarea></td>
        </tr>
    </table>
    <button type="submit" class="af-btn af-btn-primary size_m">更新</button>
    </form>

</section>

@if ($options->isNotEmpty())
<input id="tab-option" type="radio" name="tabs" class="tab-switch">
<label class="tab-label" for="tab-option">オプション</label>

<section id="optionArea" class="editSection tab-content">
    <h3>オプション
    <span>※チェックをON/OFFで即時反映されます</span></h3>
    @foreach($options as $option)
    <label>
        <input type="checkbox" name="options[]" value="{{ $option->id }}"
        {{ in_array($option->id, $selectedOptions) ? 'checked' : '' }}
        class="option-checkbox" data-option-id="{{ $option->id }}">
        {{ $option->title }}
    </label>
    @endforeach
</section>
@endif

@if ($option_pays->isNotEmpty())
<input id="tab-option-pay" type="radio" name="tabs" class="tab-switch">
<label class="tab-label" for="tab-option-pay">オプションPAY</label>

<section id="optionPayArea" class="editSection tab-content">
    <h3>オプションPAY
    <span>※チェックをON/OFFで即時反映されます</span></h3>
    @foreach($option_pays as $option_pay)
    <label>
        <input type="checkbox" name="option_pays[]" value="{{ $option_pay->id }}"
        {{ in_array($option_pay->id, $selectedOptionPays) ? 'checked' : '' }}
        class="option-pay-checkbox" data-option-pay-id="{{ $option_pay->id }}">
        {{ $option_pay->title }}
    </label>
    @endforeach
</section>
@endif

<!-- <section>
    <h3>プロフィール</h3>
    @foreach($profiles as $profile)
    <label>
        <input type="text" name="profiles[]" value="{{ $profile->title }}" class="profile-text" data-profile-id="{{ $profile->id }}">
    </label><br>
@endforeach
</section> -->


<!-- url: '{{ route("item.items.updateProfile", ["id" => $item->id]) }}', -->

@if ($profiles->isNotEmpty())
<input id="tab-profile" type="radio" name="tabs" class="tab-switch">
<label class="tab-label" for="tab-profile">プロフィール</label>

<section id="profileArea" class="editSection tab-content">
    <h3>プロフィール</h3>
    <form action="{{ route('item.items.updateProfile', $item->id) }}" method="POST">
        @csrf
        @method('patch')
        <div>
            @foreach ($profiles as $profile)
                <label>
                    <p>Q. {{ $profile->title }}</p>
                    @php
                        // キャストに紐づいたプロフィールの内容を取得
                        $content = $item->profiles->where('id', $profile->id)->first()->pivot->content ?? '';
                    @endphp
                    <input type="text" name="profiles[{{ $profile->id }}]" value="{{ $content }}">
                </label>
            @endforeach
        </div>
        <button type="submit" class="af-btn af-btn-primary size_m">更新</button>
    </form>
</section>
@endif

{{--
@if ($icon_items->isNotEmpty())
<input id="tab-icon" type="radio" name="tabs" class="tab-switch">
<label class="tab-label" for="tab-icon">キャストアイコン</label>

<section id="itemIconArea" class="editSection tab-content">
    <h3>キャストアイコン</h3>
    @foreach($icon_items as $icon_item)
    <label>
        <input type="checkbox" name="icon_items[]" value="{{ $icon_item->id }}"
        {{ in_array($icon_item->id, $selectedIconItems) ? 'checked' : '' }}
        class="iconItem-checkbox" data-iconitem-id="{{ $icon_item->id }}">
        <p>{{ $icon_item->title }}</p>

        @if($icon_item->filename)
        <p class="img"><img src="{{ asset('uploads/icon_item/' . $icon_item->filename) }}" alt=""></p>
    @endif
    </label>
    @endforeach
</section>
@endif
--}}


</div>


<script>
    $(document).ready(function() {
        // オプションのチェックボックスに変更があった場合にAjaxで反映
        $('.option-checkbox').on('change', function() {
            var optionId = $(this).data('option-id');
            var isChecked = $(this).is(':checked');
            var itemId = {{ $item->id }};

            console.log(optionId);
            $.ajax({
                url: '{{ route("item.items.updateOption", ["id" => $item->id]) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    option_id: optionId,
                    checked: isChecked
                },
                success: function(response) {
                    if (response.success) {
                        console.log('オプションが更新されました');
                    }
                },
                error: function(xhr) {
                    console.log('エラーが発生しました');
                }
            });
        });
    });

    $(document).ready(function() {
        // オプションのチェックボックスに変更があった場合にAjaxで反映
        $('.option-pay-checkbox').on('change', function() {
            var optionPayId = $(this).data('option-pay-id');
            var isCheckedPay = $(this).is(':checked');
            var itemId = {{ $item->id }};

            $.ajax({
                url: '{{ route("item.items.updateOptionPay", ["id" => $item->id]) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    option_pay_id: optionPayId,
                    pay_checked: isCheckedPay
                },
                success: function(response) {
                    if (response.success) {
                        console.log('オプションPAYが更新されました');
                    }
                },
                error: function(xhr) {
                    console.log('エラーが発生しました');
                }
            });
        });
    });


    $(document).ready(function() {
        // キャストアイコンのチェックボックスに変更があった場合にAjaxで反映
        $('.iconItem-checkbox').on('change', function() {
            var iconItem_id = $(this).data('iconitem-id');
            var isCheckedIconItem = $(this).is(':checked');
            var itemId = {{ $item->id }};

            $.ajax({
                url: '{{ route("item.items.updateIconItem", ["id" => $item->id]) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    iconItem_id: iconItem_id,
                    iconItem_checked: isCheckedIconItem
                },
                success: function(response) {
                    if (response.success) {
                        console.log('キャストアイコンが更新されました');
                    }
                },
                error: function(xhr) {
                    console.log('エラーが発生しました');
                }
            });
        });
    });


    // $(document).ready(function() {
    //     // プロフィールのチェックボックスに変更があった場合にAjaxで反映
    //     $('.profile-text').on('change', function() {
    //         var profile_id = $(this).data('profile-id');
    //         var isCheckedProfile = $(this).is(':checked');
    //         var itemId = {{ $item->id }};

    //         $.ajax({
    //             url: '{{ route("item.items.updateProfile", ["id" => $item->id]) }}',
    //             method: 'POST',
    //             data: {
    //                 _token: '{{ csrf_token() }}',
    //                 profile_id: profile_id,
    //                 profile_checked: isCheckedProfile
    //             },
    //             success: function(response) {
    //                 if (response.success) {
    //                     console.log('プロフィールが更新されました');
    //                 }
    //             },
    //             error: function(xhr) {
    //                 console.log('エラーが発生しました');
    //             }
    //         });
    //     });
    // });



</script>

</x-item-layout>
