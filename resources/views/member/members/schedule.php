<x-item-layout>


    <x-slot name="header">
            キャスト管理画面
    </x-slot>

<a href="{{ asset('item/items') }}" class="af-btn af-btn-cancel af-btn-sm">戻る</a>

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif


<section id="ImgUploader">

    <section style="margin:0 0 2rem;">
    <!-- ファイル選択フォーム -->
    <form id="imageUploadForm" enctype="multipart/form-data">
        <input type="file" id="image" name="image">
        <button type="submit" class="af-btn af-btn-primary af-btn-sm">登録</button>
    </form>
    </section>

<!-- 画像リスト -->
<div id="imageList"></div>


<style>

    /* ドラッグ中のアイテムのスタイル */
.ui-state-highlight {
    height: 1.5em;
    line-height: 1.2em;
    background: #f0f0f0;
}
</style>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- jQuery UI -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/smoothness/jquery-ui.css">


<script>

    $(document).ready(function() {
        // 画像を表示する関数
        function loadImages() {
            $.ajax({
                url: '/item/items/{{ $item->id }}/images',
                type: 'GET',
                success: function(data) {
                    $('#imageList').html(data);
                }
            });
        }

        // 初期表示で画像を読み込み
        loadImages();

        // 画像アップロード処理
        $('#imageUploadForm').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData();
            formData.append('image', $('#image')[0].files[0]);

            $.ajax({
                url: '/item/items/{{ $item->id }}/upload-image',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if(response.success) {
                        // alert('Image uploaded successfully!');
                        loadImages();  // 画像リストを再読み込み
                    } else {
                        alert('Failed to upload image.');
                    }
                },
                error: function(xhr, status, error) {
                    alert('An error occurred: ' + error);
                }
            });
        });

        // フラグの切り替えと画像削除
        $(document).on('click', '.toggle-flag, .delete-image', function() {
            var action = $(this).hasClass('toggle-flag') ? 'toggle-flag' : 'delete';
            var imageId = $(this).data('id');
            var url = '/item/items/{{ $item->id }}/image/' + action + '/' + imageId;

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if(response.success) {
                        loadImages();  // 画像リストを再読み込み
                    } else {
                        alert('Action failed.');
                    }
                }
            });
        });
    });




    $(function() {
    $("#sortable").sortable({
        placeholder: "ui-state-highlight",
        update: function(event, ui) {
            // ソート後の順序をサーバーに送信
            var order = $(this).sortable("toArray", { attribute: 'data-id' });
            $.ajax({
                url: '/update-image-order',  // ソート順序を保存するエンドポイント
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    order: order
                },
                success: function(response) {
                    alert('画像順序が更新されました');
                },
                error: function(xhr) {
                    alert('順序の更新に失敗しました');
                }
            });
        }
    });
    $("#sortable").disableSelection();
});

</script>

</section>



<section>
<!-- 画像リスト -->
<ul id="sortable">

</ul>

</section>




    <table>
    <form method="POST" action="{{ route('item.items.update', $item) }}">
        @csrf
        @method('patch')

        <tr>
            <th><label for="item_active">公開:</label></th>
            <td>
                <input type="checkbox" id="item_active" name="item_active" value="1" {{ $item->item_active ? 'checked' : '' }}>
            </td>
        </tr>
        <tr>
            <th><label for="item_name">名前:</label></th>
            <td>
                <input type="text" id="item_name" name="item_name" value="{{ $item->item_name }}" required></td>
        </tr>
        <tr>
            <th>年齢</th>
            <td>
                <select name="item_age">
                    <option velue="0"
                    @if( $item->item_age == 0 )
                     selected
                     @endif
                     > ‐‐ </option>
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
                T <select name="item_height">
                @for ($t = 135; $t <= 190; $t++)
                    <option value="{{ $t }}"
                    @if( $t == $item->item_height )
                     selected
                     @endif
                     >{{ $t }}</option>
                @endfor
                </select>&nbsp;
                B <select name="item_b">
                @for ($b = 70; $b <= 100; $b++)
                    <option value="{{ $b }}"
                    @if( $b == $item->item_b )
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
                    @if( $w == $item->item_w )
                     selected
                     @endif
                     >{{ $w }}</option>
                @endfor
                </select>&nbsp;
                H <select name="item_h">
                @for ($h = 70; $h <= 100; $h++)
                    <option value="{{ $h }}"
                    @if( $h == $item->item_h )
                     selected
                     @endif
                     >{{ $h }}</option>
                @endfor
                </select>&nbsp;
                靴 <select name="item_shoes">
                @for ($s = 22; $s <= 28; $s++)
                <option value="{{ $s }}"
                @if( $s == $item->item_shoes )
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
            <th>コメント:</th>
            <td>
                <textarea  name="item_comment">{{ $item->item_comment }}</textarea></td>
        </tr>
        <tr>
            <th>コメント2:</th>
            <td>
                <textarea  name="item_comment2">{{ $item->item_comment2 }}</textarea></td>
        </tr>

        <tr>
            <th>ブログ名:</th>
            <td>
                <input type="text" name="item_blogname" value="{{ $item->item_blogname }}" ></td>
        </tr>
        <tr>
            <th>ブログURL:</th>
            <td>
                <input type="text" name="item_blog" value="{{ $item->item_blog }}" ></td>
        </tr>
        <tr>
            <th>ツイッター:</th>
            <td>
                <input type="text"  name="item_twitter" value="{{ $item->item_twitter }}" ></td>
        </tr>
        <tr>
            <th>その他URL:</th>
            <td>
                <input type="text" name="item_url" value="{{ $item->item_url }}" ></td>
        </tr>
        <tr>
            <th>インスタグラム:</th>
            <td>
                <input type="text" name="item_instagram" value="{{ $item->item_instagram }}" ></td>
        </tr>
        <tr>
            <th>TikTok:</th>
            <td>
                <input type="text" name="item_tiktok" value="{{ $item->item_tiktok }}" ></td>
        </tr>
        <tr>
            <th>YouTube:</th>
            <td>
                <input type="text" name="item_youtube" value="{{ $item->item_youtube }}" ></td>
        </tr>
        <tr>
            <th>LINE:</th>
            <td>
                <input type="text" name="item_line" value="{{ $item->item_line }}" ></td>
        </tr>
        <tr>
            <th>メール:</th>
            <td>
                <input type="text" name="email" value="{{ $item->email }}" ></td>
        </tr>
        <tr>
            <th>電話番号:</th>
            <td>
                <input type="text" name="item_tel" value="{{ $item->item_tel }}" ></td>
        </tr>
        <tr>
            <th>パスワード:</th>
            <td>
                <input type="password" name="password" value="{{ $item->password }}" ></td>
        </tr>
        <tr>
            <th>リアルタイムコメント:</th>
            <td>
                <textarea  name="item_p_comment">{{ $item->item_p_comment }}</textarea></td>
        </tr>
        <tr>
            <td colspan="2"><button type="submit" class="af-btn af-btn-primary">更新</button></td>
        </tr>
    </form>
    </table>


</x-item-layout>
