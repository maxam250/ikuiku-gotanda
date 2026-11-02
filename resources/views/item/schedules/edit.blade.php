<x-item-layout>


    <x-slot name="header">
            キャスト管理画面
    </x-slot>

    @include('header')
    <link rel="stylesheet" type="text/css" href="css/edit_sch2.css" />

    @if ($c_cms_login)

    @php
        $get_sch_day = request()->query('day');
    @endphp

    <h1 class="logo">
        <a href="{{ url('edit_sch_sortable?day=' . $today_lag_date) }}">出勤スケジュール管理</a>
    </h1>

    <div id="edit_sch">

    <script src="{{ asset('js/jquery-1.3.1.min.js') }}"></script>

    <div class="flexSection">

    <div>
    <form method="post" action="{{ url('edit_sch_sortable') }}">
    @csrf
    <select name="day">
    <option value="{{ request()->query('day') }}">{{ request()->query('day') }}@if (empty(request()->query('day'))) {{ $sel_date->format('m月d日') }} @endif</option>
    @foreach($days as $day)
    <option value="{{ $day[1] }}" @if($day[1] == request()->query('day')) selected @endif>
    @if($day[1] == request()->query('day'))<span class="text-warning fw-bold fs-6">▼@else<a href="{{ url('edit_sch?day=' . $day[1]) }}"><span class="text-dark">@endif
    </option>
    @endforeach
    </select>
    <input type="submit" value="変更" />
    </form>
    </div>
    </div>
    @endif



</x-item-layout>
