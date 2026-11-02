<x-item-layout>


<x-slot name="header">
アカウント情報
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


<section id="itemInfoArea" >

    <table>
    <form method="POST" action="{{ route('item.items.account.accountUpdate', $item) }}">
        @csrf
        @method('patch')
        <tr>
            <th>ログインID</th>
            <td>
                {{ $item->loginid }}</td>
        </tr>
        <tr>
            <th>パスワード</th>
            <td>
                <input type="password" name="password" placeholder="変更する場合のみ入力してください" class="size_m"></td>
        </tr>
        <tr>
            <th>メール</th>
            <td>
                <input type="text" name="email" value="{{ old('email', $item->email) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>電話番号</th>
            <td>
                <input type="text" name="item_tel" value="{{ old('item_tel', $item->item_tel) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>LINE ID</th>
            <td>
                <input type="text" name="item_line" value="{{ old('item_line', $item->item_line) }}" class="size_m"></td>
        </tr>
    </table>
    <button type="submit" class="af-btn af-btn-primary size_m">更新</button>
    </form>
</section>

</x-item-layout>
