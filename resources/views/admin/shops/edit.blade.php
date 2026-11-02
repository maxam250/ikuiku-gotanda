<x-admin-layout>


<x-slot name="header">
店舗情報編集
</x-slot>

<div class="back">
    <a href="{{ route('admin.shops.index') }}" class="btn-primary">戻る</a>
</div>

<section class="MessageArea">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</section>

<section class="contentArea">
    <form action="{{ route('admin.shops.update', $shop->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
<table class="listTbl">
    <thead>
    <tr>
        <th style="width: 20%;">項目</th>
        <th>内容</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th>公開</th>
        <td>
            <input type="checkbox" id="shop_active" name="shop_active" value="1" {{ $shop->shop_active ? 'checked' : '' }}></td>
    </tr>
    <tr>
        <th>ショップ名</th>
        <td><input type="text" name="shop_name" class="form-control" value="{{ $shop->shop_name }}" required></td>
    </tr>
    <tr>
        <th>サブネーム</th>
        <td><input type="text" name="shop_sub_name" class="form-control" value="{{ $shop->shop_sub_name }}"></td>
    </tr>
    <tr>
        <th>キャッチコピー</th>
        <td><input type="text" name="shop_copy" class="form-control size_m" value="{{ $shop->shop_copy }}"></td>
    </tr>
    <tr>
        <th>エリア</th>
        <td><input type="text" name="shop_area" class="form-control" value="{{ $shop->shop_area }}"></td>
    </tr>
    @if (Auth::user()->name == 'admin')
    {{-- 管理者のみ更新可能な項目 --}}
    <tr>
        <th>ID NAME</th>
        <td><input type="text" name="shop_idname" class="form-control" value="{{ $shop->shop_idname }}" required></td>
    </tr>
    <tr>
        <th>ドメイン</th>
        <td><input type="text" name="shop_domain" class="form-control" value="{{ $shop->shop_domain }}" ></td>
    </tr>
    <tr>
        <th>アナリティクスID</th>
        <td><input type="text" name="shop_analytics" class="form-control" value="{{ $shop->shop_analytics }}" ></td>
    </tr>
    @else
    <input type="hidden" name="shop_idname" class="form-control" value="{{ $shop->shop_idname }}">
    <input type="hidden" name="shop_domain" class="form-control" value="{{ $shop->shop_domain }}">
    <input type="hidden" name="shop_analytics" class="form-control" value="{{ $shop->shop_analytics }}">
    @endif
    <tr>
        <th>URL</th>
        <td><input type="text" name="shop_url" class="form-control size_m" value="{{ $shop->shop_url }}" placeholder="https://zzz.xxx"></td>
    </tr>
    <tr>
        <th>電話番号（メイン）</th>
        <td><input type="text" name="shop_tel" class="form-control" value="{{ $shop->shop_tel }}"></td>
    </tr>
    <tr>
        <th>電話番号（サブ/求人）</th>
        <td><input type="text" name="shop_tel2" class="form-control" value="{{ $shop->shop_tel2 }}"></td>
    </tr>
    <tr>
        <th>営業時間</th>
        <td><input type="text" name="shop_open" class="form-control" value="{{ $shop->shop_open }}" ></td>
    </tr>
    <tr>
        <th>Eメール</th>
        <td><input type="text" name="shop_mail" class="form-control" value="{{ $shop->shop_mail }}"></td>
    </tr>
    <tr>
        <th>Eメール（サブ/求人）</th>
        <td><input type="text" name="shop_mail2" class="form-control" value="{{ $shop->shop_mail2 }}"></td>
    </tr>
    <tr>
        <th>住所</th>
        <td>
            〒 <input type="text" name="shop_zip1" class="form-control size_s" value="{{ $shop->shop_zip1 }}" placeholder="〇〇〇">-
            <input type="text" name="shop_zip2" class="form-control size_s" value="{{ $shop->shop_zip2 }}" placeholder="〇〇〇〇"><br><br>
            <input type="text" name="shop_address" class="form-control w_500px" value="{{ $shop->shop_address }}" placeholder="番地まで">&emsp;
            <input type="text" name="shop_address2" class="form-control w_500px" value="{{ $shop->shop_address2 }}" placeholder="建物名など">
        </td>
    </tr>
    <tr>
        <th>LINE</th>
        <td><input type="text" name="shop_line" class="form-control size_m" value="{{ $shop->shop_line }}"></td>
    </tr>
    <tr>
        <th>LINE ID</th>
        <td><input type="text" name="shop_line_id" class="form-control" value="{{ $shop->shop_line_id }}"></td>
    </tr>
    <tr>
        <th>クレジット決済リンク</th>
        <td><input type="text" name="shop_payment" class="form-control size_m" value="{{ $shop->shop_payment }}"></td>
    </tr>
    <tr>
        <th>Web予約リンク</th>
        <td><input type="text" name="shop_reservation" class="form-control size_m" value="{{ $shop->shop_reservation }}"></td>
    </tr>
    <tr>
        <th>お問い合わせリンク</th>
        <td><input type="text" name="shop_contact" class="form-control size_m" value="{{ $shop->shop_contact }}"></td>
    </tr>
    <tr>
        <th>チャットリンク</th>
        <td><input type="text" name="shop_chat" class="form-control size_m" value="{{ $shop->shop_chat }}"></td>
    </tr>
    <tr>
        <th>求人ページリンク（メイン）</th>
        <td><input type="text" name="shop_recruit" class="form-control size_m" value="{{ $shop->shop_recruit }}"></td>
    </tr>
    <tr>
        <th>求人ページリンク（サブ）</th>
        <td><input type="text" name="shop_recruit_mens" class="form-control size_m" value="{{ $shop->shop_recruit_mens }}"></td>
    </tr>
    <tr>
        <th>X（旧ツイッター）</th>
        <td><input type="text" name="shop_x" class="form-control size_m" value="{{ $shop->shop_x }}" placeholder="https://x.com/アカウント名"></td>
    </tr>
    <tr>
        <th>インスタグラム</th>
        <td><input type="text" name="shop_instagram" class="form-control size_m" value="{{ $shop->shop_instagram }}" placeholder="https://instagram.com/アカウント名"></td>
    </tr>
    <tr>
        <th>YouTube</th>
        <td><input type="text" name="shop_youtube" class="form-control size_m" value="{{ $shop->shop_youtube }}" placeholder="https://youtube.com/アカウント名"></td>
    </tr>
    <tr>
        <th>TikTok</th>
        <td><input type="text" name="shop_tiktok" class="form-control size_m" value="{{ $shop->shop_tiktok }}" placeholder="https://tiktok.com/アカウント名"></td>
    </tr>
    <tr>
        <th>FaceBook</th>
        <td><input type="text" name="shop_facebook" class="form-control size_m" value="{{ $shop->shop_facebook }}" placeholder="https://facebook.com/アカウント名"></td>
    </tr>
    <tr>
        <th>Googleマップ(アクセスページ)</th>
        <td><input type="text" name="shop_map" class="form-control" value="{{ $shop->shop_map }}"></td>
    </tr>
    <tr>
        <th>写メ日記</th>
        <td><input type="text" name="shop_shame" class="form-control size_m" value="{{ $shop->shop_shame }}"></td>
    </tr>
    <tr>
        <th>口コミ</th>
        <td><input type="text" name="shop_review" class="form-control size_m" value="{{ $shop->shop_review }}"></td>
    </tr>
    <tr>
        <th>速報</th>
        <td><input type="text" name="shop_sokuhime" class="form-control size_m" value="{{ $shop->shop_sokuhime }}"></td>
    </tr>
    <tr>
        <th>動画</th>
        <td><input type="text" name="shop_movie" class="form-control size_m" value="{{ $shop->shop_movie }}"></td>
    </tr>
    <tr>
        <th>その他リンク[1]</th>
        <td><input type="text" name="shop_link1" class="form-control size_m" value="{{ $shop->shop_link1 }}"></td>
    </tr>
    <tr>
        <th>その他リンク[2]</th>
        <td><input type="text" name="shop_link2" class="form-control size_m" value="{{ $shop->shop_link2 }}"></td>
    </tr>
    <tr>
        <th>その他リンク[3]</th>
        <td><input type="text" name="shop_link3" class="form-control size_m" value="{{ $shop->shop_link3 }}"></td>
    </tr>
    <tr>
        <th>その他リンク[4]</th>
        <td><input type="text" name="shop_link4" class="form-control size_m" value="{{ $shop->shop_link4 }}"></td>
    </tr>
    <tr>
        <th>ウィジェット[1]<p class="text-xs">トップ　PC右カラム / SP下段</p></th>
        <td>
            <textarea name="shop_wiget1" style="min-height:300px;width: 100%;">{{ $shop->shop_wiget1 }}</textarea>
        </td>
    </tr>
    <tr>
        <th>ウィジェット[2]<p class="text-xs">未設定</p></th>
        <td>
            <textarea name="shop_wiget2" style="min-height:300px;width: 100%;">{{ $shop->shop_wiget2 }}</textarea>
        </td>
    </tr>
    <tr>
        <th>ウィジェット[3]<p class="text-xs">未設定</p></th>
        <td>
            <textarea name="shop_wiget3" style="min-height:300px;width: 100%;">{{ $shop->shop_wiget3 }}</textarea>
        </td>
    </tr>
</tbody>
</table>


        <button type="submit" class="btn btn-primary"> 更新する </button>
    </form>

</section>



</x-admin-layout>
