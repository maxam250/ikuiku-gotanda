<x-item-layout>


    <x-slot name="header">
            キャスト管理画面
    </x-slot>

<a href="{{ asset('item/casts') }}" class="af-btn af-btn-cancel af-btn-sm">戻る</a>

    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif


<section id="ImgUploader">


<!-- 画像リスト -->
    <div id="uploadedImages">
    <!-- アップロードされた画像のリストがここに表示される -->
    </div>
    <section class="mb-4">
        <!-- ファイル選択フォーム -->
        <form id="imageUploadForm" enctype="multipart/form-data">
            <input type="file" name="image" id="imageInput" accept="image/*">
            <button type="submit" class="af-btn af-btn-primary af-btn-sm">登録</button>
        </form>
        </section>


<script>

// 画像のアップロード
document.getElementById('imageUploadForm').addEventListener('submit', function(e) {
    e.preventDefault();

    let formData = new FormData(this);
    let imageInput = document.getElementById('imageInput');

    fetch('/item/casts/{{ $cast->id }}/upload-image', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            // HTTPステータスコードが成功範囲外の場合
            return response.json().then(err => {
                // エラーメッセージをサーバーから取得して投げる
                throw new Error(err.message || 'サーバーエラー');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // 画像のリストに新しい画像を追加
            let uploadedImages = document.getElementById('uploadedImages');
            let imageElement = `
                <div>
                    <img src="${data.image_url}" alt="Uploaded Image" class="w_90px">
                    <label>
                        <input type="radio" name="thumbnail_flag" value="${data.image_id}"> サムネイルに設定
                    </label>
                </div>
            `;
            uploadedImages.innerHTML += imageElement;
        } else {
            // サーバーからのエラーメッセージを表示
            alert(data.message || 'アップロードに失敗しました');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('エラー: ' + error.message);
    });
});

//画像の削除
function deleteImage(imageId) {
    if (confirm('この画像を削除してもよろしいですか？')) {
        fetch(`/item/casts/images/${imageId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const imageElement = document.getElementById('image-' + imageId);
                // フェードアウトアニメーションを追加
                imageElement.classList.add('fadeOut');
                // アニメーションが終了した後に要素を削除
                imageElement.addEventListener('animationend', () => {
                    imageElement.remove();
                });
            } else {
                alert('画像の削除に失敗しました。');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('エラーが発生しました。');
        });
    }
}




//サムネイルの設定
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('input[name="thumbnail_flag"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const imageId = this.value;
            fetch('/item/casts/{{ $cast->id }}/set-thumbnail', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // CSRF token
                },
                body: JSON.stringify({ image_id: imageId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('サムネイルが更新されました。');
                } else {
                    alert('エラーが発生しました。');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('エラーが発生しました。');
            });
        });
    });
});


//画像の更新
function replaceImage(imageId) {
    var form = document.getElementById('replaceForm-' + imageId);
    var formData = new FormData(form);

    fetch(`/item/casts/images/${imageId}/replace`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 画像を新しいものに更新
            document.querySelector(`#image-${imageId} img`).src = data.newImageUrl;
        } else {
            alert('画像の差し替えに失敗しました。');
        }
    })
    .catch(error => console.error('Error:', error));
}


</script>




<style>

    /* ドラッグ中のアイテムのスタイル */
.ui-state-highlight {
    height: 1.5em;
    line-height: 1.2em;
    background: #f0f0f0;
}
</style>


</section>



<section>
<!-- 画像リスト -->
<ul id="sortable">
    @foreach ($images as $image)
    <div id="image-{{ $image->id }}">
        <img src="{{ asset('uploads/' . $image->filename) }}" alt="Image" class="w_200px">

        <!-- 画像差し替えフォーム -->
        <form id="replaceForm-{{ $image->id }}" enctype="multipart/form-data">
            <input type="file" name="image" onchange="replaceImage({{ $image->id }})">
        </form>

        <label>
            <input type="radio" name="thumbnail_flag" value="{{ $image->id }}" {{ $image->flag ? 'checked' : '' }}> サムネイルに設定
        </label>
        <button onclick="deleteImage({{ $image->id }})">削除</button>


    </div>
@endforeach
</ul>

</section>


<section>

    <table>
    <form method="POST" action="{{ route('item.casts.update', $cast) }}">
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
                    <option value="0"
                    @if( $cast->cast_age == 0 )
                     selected
                     @endif
                     > 未設定 </option>
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
            @php
                //初期値の設定
                $df_height = $cast->cast_height ?? 170;
                $df_b = $cast->cast_b ?? 85;
                $df_w = $cast->cast_w ?? 58;
                $df_h = $cast->cast_h ?? 85;
                $df_s = $cast->cast_shoes ?? 25;
            @endphp

                T <select name="cast_height">
                @for ($t = 135; $t <= 190; $t++)
                    <option value="{{ $t }}"
                    @if( $t == $df_height )
                     selected
                    @endif
                     >{{ $t }}</option>
                @endfor
                </select>&nbsp;
                B <select name="cast_b">
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
                    @if( $w == $df_w )
                     selected
                    @endif
                     >{{ $w }}</option>
                @endfor
                </select>&nbsp;
                H <select name="cast_h">
                @for ($h = 70; $h <= 100; $h++)
                    <option value="{{ $h }}"
                    @if( $h == $df_h )
                     selected
                    @endif
                     >{{ $h }}</option>
                @endfor
                </select>&nbsp;
                靴 <select name="cast_shoes">
                @for ($s = 22; $s <= 28; $s++)
                <option value="{{ $s }}"
                @if( $s == $df_s )
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

</section>


<section>
    <h3>オプション</h3>
    @foreach($options as $option)
    <label>
        <input type="checkbox" name="options[]" value="{{ $option->id }}"
        {{ in_array($option->id, $selectedOptions) ? 'checked' : '' }}
        class="option-checkbox" data-option-id="{{ $option->id }}">
        {{ $option->title }}
    </label><br>
@endforeach
</section>

<script>
    $(document).ready(function() {
        // オプションのチェックボックスに変更があった場合にAjaxで反映
        $('.option-checkbox').on('change', function() {
            var optionId = $(this).data('option-id');
            var isChecked = $(this).is(':checked');
            var castId = {{ $cast->id }};

            $.ajax({
                url: '{{ route("item.casts.updateOption", ["id" => $cast->id]) }}',
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
</script>

</x-item-layout>
