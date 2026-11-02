<x-admin-layout>


    <x-slot name="header">
            キャスト管理画面
    </x-slot>

<a href="{{ route('admin.casts.index') }}">戻る</a>

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
        <button type="submit">Upload</button>
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
                url: '/kanrigamen/casts/{{ $cast->id }}/images',
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
                url: '/kanrigamen/casts/{{ $cast->id }}/upload-image',
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
            var url = '/kanrigamen/casts/{{ $cast->id }}/image/' + action + '/' + imageId;

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
    <form method="POST" action="{{ route('admin.casts.update', $cast) }}">
        @csrf
        @method('patch')

        <tr>
            <th><label for="cast_active">公開:</label></th>
            <td>
                <input type="checkbox" id="cast_active" name="cast_active" value="1" {{ $cast->cast_active ? 'checked' : '' }}>
            </td>
        </tr>
        <tr>
            <th><label for="cast_name">名前:</label></th>
            <td>
                <input type="text" id="cast_name" name="cast_name" value="{{ $cast->cast_name }}" required></td>
        </tr>
        <tr>
            <th>年齢</th>
            <td>
                <select name="cast_age">
                    <option velue="0"
                    @if( $cast->cast_age == 0 )
                     selected
                     @endif
                     > ‐‐ </option>
                    @for ($a = 18; $a <= 40; $a++)
                        <option value="{{ $a }}"
                        @if( $a == $cast->cast_age )
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
                T <select name="cast_height">
                @for ($t = 135; $t <= 190; $t++)
                    <option value="{{ $t }}"
                    @if( $t == $cast->cast_height )
                     selected
                     @endif
                     >{{ $t }}</option>
                @endfor
                </select>&nbsp;
                B <select name="cast_b">
                @for ($b = 70; $b <= 100; $b++)
                    <option value="{{ $b }}"
                    @if( $b == $cast->cast_b )
                     selected
                     @endif
                     >{{ $b }}</option>
                @endfor
                </select>&nbsp;
                @php
                $cups = range('A', 'K');
                @endphp
                CUP <select name="cast_cup">
                    @foreach ($cups as $cup)
                        <option value="{{ $cup }}"
                        @if( $cup == $cast->cast_cup )
                         selected
                         @endif
                         >{{ $cup }}</option>
                    @endforeach
                 </select>&nbsp;

                W <select name="cast_w">
                @for ($w = 50; $w <= 90; $w++)
                    <option value="{{ $w }}"
                    @if( $w == $cast->cast_w )
                     selected
                     @endif
                     >{{ $w }}</option>
                @endfor
                </select>&nbsp;
                H <select name="cast_h">
                @for ($h = 70; $h <= 100; $h++)
                    <option value="{{ $h }}"
                    @if( $h == $cast->cast_h )
                     selected
                     @endif
                     >{{ $h }}</option>
                @endfor
                </select>&nbsp;
                靴 <select name="cast_shoes">
                @for ($s = 22; $s <= 28; $s++)
                <option value="{{ $s }}"
                @if( $s == $cast->cast_shoes )
                 selected
                 @endif
                 >{{ $s }}</option>
                 <option value="{{ $s }}.5"
                 @if( ($s.".5") == $cast->cast_shoes )
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
                <textarea  name="cast_comment">{{ $cast->cast_comment }}</textarea></td>
        </tr>
        <tr>
            <th>コメント2:</th>
            <td>
                <textarea  name="cast_comment2">{{ $cast->cast_comment2 }}</textarea></td>
        </tr>

        <tr>
            <th>ブログ名:</th>
            <td>
                <input type="text" name="cast_blogname" value="{{ $cast->cast_blogname }}" ></td>
        </tr>
        <tr>
            <th>ブログURL:</th>
            <td>
                <input type="text" name="cast_blog" value="{{ $cast->cast_blog }}" ></td>
        </tr>
        <tr>
            <th>ツイッター:</th>
            <td>
                <input type="text"  name="cast_twitter" value="{{ $cast->cast_twitter }}" ></td>
        </tr>
        <tr>
            <th>その他URL:</th>
            <td>
                <input type="text" name="cast_url" value="{{ $cast->cast_url }}" ></td>
        </tr>
        <tr>
            <th>インスタグラム:</th>
            <td>
                <input type="text" name="cast_instagram" value="{{ $cast->cast_instagram }}" ></td>
        </tr>
        <tr>
            <th>TikTok:</th>
            <td>
                <input type="text" name="cast_tiktok" value="{{ $cast->cast_tiktok }}" ></td>
        </tr>
        <tr>
            <th>YouTube:</th>
            <td>
                <input type="text" name="cast_youtube" value="{{ $cast->cast_youtube }}" ></td>
        </tr>
        <tr>
            <th>LINE:</th>
            <td>
                <input type="text" name="cast_line" value="{{ $cast->cast_line }}" ></td>
        </tr>
        <tr>
            <th>メール:</th>
            <td>
                <input type="text" name="cast_mail" value="{{ $cast->cast_mail }}" ></td>
        </tr>
        <tr>
            <th>電話番号:</th>
            <td>
                <input type="text" name="cast_tel" value="{{ $cast->cast_tel }}" ></td>
        </tr>
        <tr>
            <th>パスワード:</th>
            <td>
                <input type="password" name="cast_password" value="{{ $cast->cast_password }}" ></td>
        </tr>
        <tr>
            <th>リアルタイムコメント:</th>
            <td>
                <textarea  name="cast_p_comment">{{ $cast->cast_p_comment }}</textarea></td>
        </tr>
        <tr>
            <td colspan="2"><button type="submit">　更新　</button></td>
        </tr>
    </form>
    </table>


</x-admin-layout>
