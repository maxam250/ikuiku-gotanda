
<x-admin-layout>


<x-slot name="header">
 店舗情報追加
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

    <form action="{{ route('admin.shops.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="shop_active">表示</label>
            <div class="unit">
            <input type="checkbox" name="shop_active" value="1" checked }}>
            </div>
        </div>

        <div class="form-group">
            <label for="shop_name">ショップ名</label>
            <input type="text" name="shop_name" class="form-control" required value="{{ old('shop_name' ?? '') }}">
        </div>

        <div class="form-group">
            <label for="shop_domain">ドメイン</label>
            <input type="text" name="shop_domain" class="form-control" value="{{ old('shop_domain' ?? '') }}" placeholder="xxxxx.com">
        </div>
        <button type="submit" class="admin-btn admin-btn-submit">
            <i class="fas fa-plus"></i>ショップ登録
        </button>
    </form>
</section>

</x-admin-layout>
