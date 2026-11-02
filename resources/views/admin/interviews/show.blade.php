<x-admin-layout>
    <x-slot name="header">
        Q&A詳細
    </x-slot>

    <section class="PageTransitionArea">
        <div class="back">
            <a href="{{ route('admin.interviews.index') }}">戻る</a>
        </div>
    </section>

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

    <section class="editSection">
        <h3>Q&A詳細</h3>
        
        <table>
            <tr>
                <th>ID:</th>
                <td>{{ $interview->id }}</td>
            </tr>
            <tr>
                <th>質問:</th>
                <td>{{ $interview->title }}</td>
            </tr>
            <tr>
                <th>並び順:</th>
                <td>{{ $interview->sort }}</td>
            </tr>
            <tr>
                <th>作成日時:</th>
                <td>{{ $interview->created_at }}</td>
            </tr>
            <tr>
                <th>更新日時:</th>
                <td>{{ $interview->updated_at }}</td>
            </tr>
        </table>

        <div class="button-group">
            <a href="{{ route('admin.interviews.edit', $interview->id) }}" class="btn">編集</a>
            <a href="{{ route('admin.interviews.index') }}" class="btn">一覧に戻る</a>
        </div>
    </section>
</x-admin-layout> 