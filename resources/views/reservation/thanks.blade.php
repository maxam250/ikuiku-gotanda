@extends('layouts.app')

@section('breadcrumb_label', 'ご予約申し込み完了')

@section('content')
<div class="titleArea">
    <h2>RESERVATION</h2>
    <div class="title">ご予約申し込み完了</div>
</div>

<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-lg shadow-md p-8">
            <div class="text-center">
                <!-- 成功アイコン -->
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-6">
                    <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-4">予約申し込み完了</h1>

                <div class="text-gray-600 mb-8">
                    <p class="mb-4">ご予約ありがとうございました。</p>
                    <p class="mb-4">確認メールをお送りしましたのでご確認ください。</p>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-yellow-800 text-sm">
                            <strong>※重要：</strong>まだ予約は確定していませんので、確認後ご連絡いたします。
                        </p>
                    </div>
                </div>

                <!-- ボタン -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('top') }}"
                        class="inline-flex items-center px-6 py-3 bg-blue-300 hover:bg-blue-400 text-white font-medium rounded-lg transition duration-200 transform hover:scale-101">
                        <i class="fas fa-home mr-2"></i>
                        ホームに戻る
                    </a>
                    <a href="{{ route('reservation.index') }}"
                        class="inline-flex items-center px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition duration-200">
                        <i class="fas fa-calendar-plus mr-2"></i>
                        別の予約をする
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ブラウザの戻るボタンを無効にする
history.pushState(null, null, location.href);
window.addEventListener('popstate', function() {
    history.pushState(null, null, location.href);
});

// フォームの再送信を防ぐ
if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
}
</script>

@endsection
