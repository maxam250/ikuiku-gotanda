@extends('layouts.app')

@section('content')

<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- タイトルとフォームのコンテナ -->
        <div class="lg:flex lg:gap-8">
            <!-- タイトルセクション -->
            <div class="lg:w-1/3 mb-8 lg:mb-0">
                <div class="bg-white rounded-lg shadow-md p-6 lg:p-8">
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-4">予約フォーム</h1>
                    <p class="text-gray-600 leading-relaxed">
                        撮影のご予約をお受けいたします。<br>
                        必要事項をご記入の上、確認画面にお進みください。
                    </p>
                    
                    @if(Auth::guard('member')->check())
                    <!-- 会員情報表示 -->
                    <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <p class="text-blue-800 font-semibold">
                            <i class="fas fa-user mr-2"></i>
                            {{ Auth::guard('member')->user()->name }} 様
                        </p>
                        <p class="text-blue-600 text-sm mt-1">会員としてログイン中</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- フォームセクション -->
            <div class="lg:w-2/3">
                <div class="bg-white rounded-lg shadow-md p-6 lg:p-8">
                    <form method="POST" action="{{ route('reservation.confirm') }}">
                        @csrf
                        <input type="hidden" name="type" value="{{ request('type') }}">

                        @if(!Auth::guard('member')->check())
                        <!-- ビジター用フォーム項目 -->
                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    お名前 <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('name')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    メールアドレス <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('email')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                電話番号 <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-2">
                                <input type="tel" name="tel[]" value="{{ old('tel.0') }}" required maxlength="4"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-center">
                                <span class="flex items-center text-gray-500">-</span>
                                <input type="tel" name="tel[]" value="{{ old('tel.1') }}" required maxlength="4"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-center">
                                <span class="flex items-center text-gray-500">-</span>
                                <input type="tel" name="tel[]" value="{{ old('tel.2') }}" required maxlength="4"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-center">
                            </div>
                            @error('tel.*')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        @endif

                        <!-- 共通フォーム項目 -->
                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                                    予約日 <span class="text-red-500">*</span>
                                </label>
                                <input type="date" id="date" name="date" value="{{ old('date') }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('date')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="time_start" class="block text-sm font-medium text-gray-700 mb-2">
                                    予約時間 <span class="text-red-500">*</span>
                                </label>
                                <select name="time_start" id="time_start" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">選択してください</option>
                                    @for ($hour = 10; $hour <= 19; $hour++)
                                        <option value="{{ sprintf('%02d:00:00', $hour) }}" {{ old('time_start') == sprintf('%02d:00:00', $hour) ? 'selected' : '' }}>
                                            {{ sprintf('%02d:00', $hour) }}
                                        </option>
                                    @endfor
                                </select>
                                @error('time_start')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="item_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    撮影アイテム <span class="text-red-500">*</span>
                                </label>
                                <select name="item_id" id="item_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">選択してください</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}" {{ old('item_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->item_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('item_id')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    コース <span class="text-red-500">*</span>
                                </label>
                                <select name="course_id" id="course_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">選択してください</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->course_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="course_time" class="block text-sm font-medium text-gray-700 mb-2">
                                コース時間 <span class="text-red-500">*</span>
                            </label>
                            <select name="course_time" id="course_time" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">選択してください</option>
                                @foreach($course_fees as $fee)
                                    <option value="{{ $fee->id }}" {{ old('course_time') == $fee->id ? 'selected' : '' }}>
                                        {{ $fee->course_time }}分 {{ number_format($fee->price) }}円
                                    </option>
                                @endforeach
                            </select>
                            @error('course_time')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">オプション</label>
                            <div class="grid md:grid-cols-2 gap-3">
                                @foreach($options as $option)
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox" name="option[]" value="{{ $option->id }}"
                                            {{ is_array(old('option')) && in_array($option->id, old('option')) ? 'checked' : '' }}
                                            class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <span class="text-sm">
                                            {{ $option->option_name }}
                                            <span class="text-blue-600 font-medium">({{ number_format($option->price) }}円)</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @error('option')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">有料オプション</label>
                            <div class="grid md:grid-cols-2 gap-3">
                                @foreach($option_pays as $option)
                                    <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox" name="option_pay[]" value="{{ $option->id }}"
                                            {{ is_array(old('option_pay')) && in_array($option->id, old('option_pay')) ? 'checked' : '' }}
                                            class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <span class="text-sm">
                                            {{ $option->option_name }}
                                            <span class="text-blue-600 font-medium">({{ number_format($option->price) }}円)</span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @error('option_pay')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">衣装</label>
                            <div class="grid md:grid-cols-3 gap-4">
                                @for($i = 1; $i <= 3; $i++)
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">衣装{{ $i }}</label>
                                        <select name="costume{{ $i }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            <option value="">選択してください</option>
                                            @foreach($costumes as $costume)
                                                <option value="{{ $costume->id }}" {{ old("costume{$i}") == $costume->id ? 'selected' : '' }}>
                                                    {{ $costume->costume_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endfor
                            </div>
                            @error('costume.*')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-8">
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">備考</label>
                            <textarea id="message" name="message" rows="4" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none">{{ old('message') }}</textarea>
                            @error('message')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4">
                            <button type="submit" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 transform hover:scale-105">
                                <i class="fas fa-check mr-2"></i>
                                確認画面へ
                            </button>
                            <a href="{{ route('reservation.select') }}" 
                                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 px-6 rounded-lg text-center transition duration-200">
                                <i class="fas fa-arrow-left mr-2"></i>
                                戻る
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', function() {
    const courseSelect = document.getElementById('course_id');
    const courseTimeSelect = document.getElementById('course_time');

    courseSelect.addEventListener('change', function() {
        const courseId = this.value;
        if (courseId) {
            fetch(`/get-course-fees/${courseId}`)
                .then(response => response.json())
                .then(data => {
                    courseTimeSelect.innerHTML = '<option value="">選択してください</option>';
                    data.forEach(fee => {
                        courseTimeSelect.innerHTML += `
                            <option value="${fee.id}">
                                ${fee.course_time}分 ${new Intl.NumberFormat('ja-JP').format(fee.price)}円
                            </option>
                        `;
                    });
                });
        } else {
            courseTimeSelect.innerHTML = '<option value="">選択してください</option>';
        }
    });
});
</script>

@endsection
