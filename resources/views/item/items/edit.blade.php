<x-item-layout>
<link rel="stylesheet" href="{{ asset('css/form.css') }}">


<x-slot name="header">
プロフィール編集
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

<section id="imageList">
    <div class="mb-2">
        写真の更新はスタッフまでご連絡ください。
    </div>
    <!-- 画像リスト -->
    <ul class="grid grid-cols-3 mb:grid-cols-4 gap-3">
        @foreach ($images as $image)
        <li class="w-full border-0 bg-gray-200" id="image-{{ $image->id }}">
                <img src="{{ Storage::url( $image->filename) }}" alt="Image">
        </li>
        @endforeach
    </ul>
</section>



<div class="tab-wrap">



<input id="tab-info" type="radio" name="tabs" class="tab-switch" checked>
<label class="tab-label" for="tab-info">キャスト情報</label>

<section id="itemInfoArea" class="editSection tab-content">

    <table>
    <form method="POST" action="{{ route('item.items.update', $item) }}">
        @csrf
        @method('patch')
        {{--
        <tr>
            <th><label for="item_cate">可能コース:</label></th>
            <td class="form_parts">
                <div class="course-settings">
                    @foreach($courses as $course)
                        @php
                            $currentStatus = isset($courseStatuses[$course->id]) ? $courseStatuses[$course->id] : 'ng';
                        @endphp
                        <div class="course-item" data-course-id="{{ $course->id }}">
                            <span class="course-name">{{ $course->title }}</span>
                            <div class="course-status">
                                <button type="button" class="status-btn {{ $currentStatus == 'ok' ? 'active' : '' }}"
                                        data-status="ok" data-course-id="{{ $course->id }}" onclick="updateCourseStatus({{ $item->id }}, {{ $course->id }}, 'ok')">
                                    OK
                                </button>
                                <button type="button" class="status-btn {{ $currentStatus == 'ng' ? 'active' : '' }}"
                                        data-status="ng" data-course-id="{{ $course->id }}" onclick="updateCourseStatus({{ $item->id }}, {{ $course->id }}, 'ng')">
                                    NG
                                </button>
                                <button type="button" class="status-btn {{ $currentStatus == 'confirm' ? 'active' : '' }}"
                                        data-status="confirm" data-course-id="{{ $course->id }}" onclick="updateCourseStatus({{ $item->id }}, {{ $course->id }}, 'confirm')">
                                    要確認
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </td>
        </tr>
        --}}
        <!-- <tr>
            <th><label for="item_active">公開:</label></th>
            <td>
                <label><input type="checkbox" id="item_active" name="item_active" value="1" {{ $item->item_active ? 'checked' : '' }}>&emsp;チェックを入れると公開されます</label>
            </td>
        </tr> -->
        <!-- <tr>
            <th><label for="item_cate">カテゴリ:</label></th>
            <td class="form_parts">
                <input type="radio" name="item_cate" id="item_cate0" value="0" {{ $item->item_cate == 0 ? 'checked' : '' }}>&emsp;<label class="radio" for="item_cate0">ノーマル</label>
                <input type="radio" name="item_cate" id="item_cate1" value="1" {{ $item->item_cate == 1 ? 'checked' : '' }}>&emsp;<label class="radio" for="item_cate1">プレミア</label>
                <input type="radio" name="item_cate" id="item_cate2" value="2" {{ $item->item_cate == 2 ? 'checked' : '' }}>&emsp;<label class="radio" for="item_cate2">プラチナ</label>
                <input type="radio" name="item_cate" id="item_cate3" value="3" {{ $item->item_cate == 3 ? 'checked' : '' }}>&emsp;<label class="radio" for="item_cate3">バナー</label>
            </td>
        </tr> -->
        <tr>
            <th><label for="item_name">名前:</label></th>
            <td>{{ $item->item_name }}
                <input type="hidden" name="item_name" value="{{ $item->item_name }}" required></td>
        </tr>
        <tr>
            <th>年齢</th>
            <td>
                <select name="item_age" class="w-auto">
                    <option value="0"
                    @if( $item->item_age == 0 )
                     selected
                     @endif
                     > 未設定 </option>
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
            <th>身長</th>
            <td>
                @php
                    $df_height = $item->item_height;
                @endphp
                <select name="item_height" class="w_120px">
                    <option value=""
                    @if( $item->item_height == '-' )
                         selected
                    @endif
                         >-</option>
                @for ($t = 135; $t <= 190; $t++)
                    <option value="{{ $t }}"
                    @if( $t == $df_height )
                     selected
                    @endif
                     >{{ $t }}</option>
                @endfor
                </select>
            </td>
        </tr>
        {{--
        <tr>
            <th>靴サイズ</th>
            <td>
                @php
                    $df_s = $item->item_shoes;
                @endphp
                <select name="item_shoes" class="w_120px">
                    <option value=""
                    @if( empty($item->item_shoes) )
                         selected
                    @endif
                         >-</option>
                @for ($s = 22; $s <= 28; $s++)
                <option value="{{ $s }}"
                @if( $s == $df_s )
                 selected
                @endif
                 >{{ $s }}</option>
                 <option value="{{ $s }}.5"
                 @if( ($s.".5") == $item->item_shoes )
                  selected
                  @endif
                  >{{ $s }}.5</option>
                @endfor
                </select>
            </td>
        </tr>
        --}}
        <tr>
            <th>スリーサイズ</th>
            <td>
                @php
                    $df_b = $item->item_b;
                    $df_w = $item->item_w;
                    $df_h = $item->item_h;
                    $cups = range('A', 'K');
                @endphp
                B <select name="item_b" class="w_80px">
                    <option value=""
                    @if( empty($item->item_b) )
                         selected
                    @endif
                         >-</option>
                @for ($b = 70; $b <= 100; $b++)
                    <option value="{{ $b }}"
                    @if( $b == $df_b )
                     selected
                    @endif
                     >{{ $b }}</option>
                @endfor
                </select>&nbsp;
                CUP <select name="item_cup" class="w_80px">
                    <option value="-"
                    @if( $item->item_cup == '-' )
                         selected
                    @endif
                         >-</option>
                    @foreach ($cups as $cup)
                        <option value="{{ $cup }}"
                        @if( $cup == $item->item_cup )
                         selected
                         @endif
                         >{{ $cup }}</option>
                    @endforeach
                 </select>&nbsp;
                W <select name="item_w" class="w_80px">
                    <option value=""
                    @if( empty($item->item_w) )
                         selected
                    @endif
                         >-</option>
                @for ($w = 50; $w <= 90; $w++)
                    <option value="{{ $w }}"
                    @if( $w == $df_w )
                     selected
                    @endif
                     >{{ $w }}</option>
                @endfor
                </select>&nbsp;
                H <select name="item_h" class="w_80px">
                    <option value=""
                    @if( empty($item->item_h) )
                         selected
                    @endif
                         >-</option>
                @for ($h = 70; $h <= 100; $h++)
                    <option value="{{ $h }}"
                    @if( $h == $df_h )
                     selected
                    @endif
                     >{{ $h }}</option>
                @endfor
                </select>
            </td>
        </tr>
        {{--
        <tr>
            <th>あいさつコメント</th>
            <td>
                <textarea id="item_comment2" name="item_comment2">{{ old('item_comment2', $item->item_comment2) }}</textarea></td>
        </tr>
        --}}
        {{--
        <tr>
            <th>ブログ名</th>
            <td>
                <input type="text" name="item_blogname" value="{{ old('item_blogname', $item->item_blogname) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>ブログURL</th>
            <td>
                <input type="text" name="item_blog" value="{{ old('item_blog', $item->item_blog) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>ツイッター</th>
            <td>
                <input type="text"  name="item_twitter" value="{{ old('item_twitter', $item->item_twitter) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>その他URL</th>
            <td>
                <input type="text" name="item_url" value="{{ old('item_url', $item->item_url) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>インスタグラム</th>
            <td>
                <input type="text" name="item_instagram" value="{{ old('item_instagram', $item->item_instagram) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>TikTok</th>
            <td>
                <input type="text" name="item_tiktok" value="{{ old('item_tiktok', $item->item_tiktok) }}" class="size_m"></td>
        </tr>
        <tr>
            <th>YouTube</th>
            <td>
                <input type="text" name="item_youtube" value="{{ old('item_youtube', $item->item_youtube) }}" class="size_m"></td>
        </tr>
        --}}
    </table>
    <button type="submit" class="af-btn af-btn-primary">
        <i class="fas fa-save"></i>更新
    </button>
    </form>

</section>
{{--
@if ($options->isNotEmpty())
<input id="tab-option" type="radio" name="tabs" class="tab-switch">
<label class="tab-label" for="tab-option">オプション</label>

<section id="optionArea" class="editSection tab-content">
    <h3>オプション
    <span>※チェックをON/OFFで即時反映されます</span></h3>
    @foreach($options as $option)
    <label>
        <input type="checkbox" name="options[]" value="{{ $option->id }}"
        {{ in_array($option->id, $selectedOptions) ? 'checked' : '' }}
        class="option-checkbox" data-option-id="{{ $option->id }}">
        {{ $option->title }}
    </label>
    @endforeach
</section>
@endif
--}}
{{--
@if ($option_pays->isNotEmpty())
<input id="tab-option-pay" type="radio" name="tabs" class="tab-switch">
<label class="tab-label" for="tab-option-pay">オプションPAY</label>

<section id="optionPayArea" class="editSection tab-content">
    <h3>オプションPAY
    <span>※チェックをON/OFFで即時反映されます</span></h3>
    @foreach($option_pays as $option_pay)
    <label>
        <input type="checkbox" name="option_pays[]" value="{{ $option_pay->id }}"
        {{ in_array($option_pay->id, $selectedOptionPays) ? 'checked' : '' }}
        class="option-pay-checkbox" data-option-pay-id="{{ $option_pay->id }}">
        {{ $option_pay->title }}
    </label>
    @endforeach
</section>
@endif
--}}
<!-- <section>
    <h3>プロフィール</h3>
    @foreach($profiles as $profile)
    <label>
        <input type="text" name="profiles[]" value="{{ $profile->title }}" class="profile-text" data-profile-id="{{ $profile->id }}">
    </label><br>
@endforeach
</section> -->


<!-- url: '{{ route("item.items.updateProfile", ["id" => $item->id]) }}', -->

@if ($profiles->isNotEmpty())
<input id="tab-profile" type="radio" name="tabs" class="tab-switch">
<label class="tab-label" for="tab-profile">プロフィール</label>

<section id="profileArea" class="editSection tab-content">
    <h3>プロフィール</h3>
    <form action="{{ route('item.items.updateProfile', $item->id) }}" method="POST">
        @csrf
        @method('patch')
        <div>
            @foreach ($profiles as $profile)
                <label>
                    <p>Q. {{ $profile->title }}</p>
                    @php
                        // キャストに紐づいたプロフィールの内容を取得
                        $content = $item->profiles->where('id', $profile->id)->first()->pivot->content ?? '';
                    @endphp
                    <input type="text" name="profiles[{{ $profile->id }}]" value="{{ $content }}">
                </label>
            @endforeach
        </div>
        <button type="submit" class="af-btn af-btn-primary">
            <i class="fas fa-save"></i>更新
        </button>
    </form>
</section>
@endif

@if ($interviews->isNotEmpty())
<input id="tab-interview" type="radio" name="tabs" class="tab-switch">
<label class="tab-label" for="tab-interview">Q&A</label>

<section id="interviewArea" class="editSection tab-content">
    <h3>Q&A</h3>
    <form action="{{ route('item.items.updateInterview', $item->id) }}" method="POST">
        @csrf
        @method('patch')
        <div>
            @foreach ($interviews as $interview)
                <div class="mb-2">
                    <p class="p-2 bg-gray-100">Q. {{ $interview->title }}</p>
                    @php
                        // キャストに紐づいたインタビューの内容を取得
                        $content = $item->interviews->where('id', $interview->id)->first()->pivot->content ?? '';
                    @endphp
                    <input type="text" name="interviews[{{ $interview->id }}]" value="{{ $content }}" class="w-full">
                </div>
            @endforeach
        </div>
        <button type="submit" class="af-btn af-btn-primary">
            <i class="fas fa-save"></i>更新
        </button>
    </form>
</section>
@endif


<!-- 料金設定タブ -->
@if(isset($settings) && $settings->pricing_type == 'individual')
<input id="tab-fee" type="radio" name="tabs" class="tab-switch">
<label class="tab-label" for="tab-fee">料金設定</label>

<section id="feeArea" class="editSection tab-content">
    <h3>料金設定</h3>
    <div class="fee-settings">
        <p class="mb-4">
            料金設定はコースごとに行います。<br>
            「一律料金を使用」をONにすると、全ての時間帯で一律の料金が適用されます。<br>
            料金を入力すると即時で確定されます。
        </p>
        @foreach($courses as $course)
            @php
                $feeSettings = $courseFeeSettings[$course->id] ?? [];
                $fee_60 = $feeSettings['fee_60'] ?? '';
                $fee_90 = $feeSettings['fee_90'] ?? '';
                $fee_120 = $feeSettings['fee_120'] ?? '';
                $fee_150 = $feeSettings['fee_150'] ?? '';
                $fee_180 = $feeSettings['fee_180'] ?? '';
                $fee_extension = $feeSettings['fee_extension'] ?? '';
                $uniform_rate = $feeSettings['uniform_rate'] ?? '';
                $use_uniform_rate = $feeSettings['use_uniform_rate'] ?? false;
            @endphp
            <div class="course-fee-item" data-course-id="{{ $course->id }}">
                <h4 class="course-name">{{ $course->title }}</h4>

                <!-- 一律料金設定 -->
                <div class="uniform-rate-section mb-4 p-3 bg-gray-50 rounded">
                    <label class="flex items-center mb-2">
                        <input type="checkbox" class="uniform-rate-checkbox"
                               data-course-id="{{ $course->id }}"
                               {{ $use_uniform_rate ? 'checked' : '' }}>
                        <span class="ml-2">一律料金を使用</span>
                    </label>
                    <div class="flex items-center">
                        <input type="number" class="uniform-rate-input w-24 mr-2"
                               placeholder="料金" value="{{ $uniform_rate }}"
                               data-course-id="{{ $course->id }}">
                        <span>円（30分単位）</span>
                    </div>
                </div>

                <!-- 料金テーブル -->
                <div class="fee-table">
                    <table>
                        <thead>
                            <tr>
                                <th>コース名</th>
                                <th>60分</th>
                                <th>90分</th>
                                <th>120分</th>
                                <th>150分</th>
                                <th>180分</th>
                                <th>延長30分</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $course->title }}</td>
                                <td>
                                    <input type="number" class="fee-input"
                                           placeholder="料金" value="{{ $fee_60 }}"
                                           data-course-id="{{ $course->id }}" data-fee-type="fee_60">
                                </td>
                                <td>
                                    <input type="number" class="fee-input"
                                           placeholder="料金" value="{{ $fee_90 }}"
                                           data-course-id="{{ $course->id }}" data-fee-type="fee_90">
                                </td>
                                <td>
                                    <input type="number" class="fee-input"
                                           placeholder="料金" value="{{ $fee_120 }}"
                                           data-course-id="{{ $course->id }}" data-fee-type="fee_120">
                                </td>
                                <td>
                                    <input type="number" class="fee-input"
                                           placeholder="料金" value="{{ $fee_150 }}"
                                           data-course-id="{{ $course->id }}" data-fee-type="fee_150">
                                </td>
                                <td>
                                    <input type="number" class="fee-input"
                                           placeholder="料金" value="{{ $fee_180 }}"
                                           data-course-id="{{ $course->id }}" data-fee-type="fee_180">
                                </td>
                                <td>
                                    <input type="number" class="fee-input"
                                           placeholder="料金" value="{{ $fee_extension }}"
                                           data-course-id="{{ $course->id }}" data-fee-type="fee_extension">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif


{{--
@if ($icon_items->isNotEmpty())
<input id="tab-icon" type="radio" name="tabs" class="tab-switch">
<label class="tab-label" for="tab-icon">キャストアイコン</label>

<section id="itemIconArea" class="editSection tab-content">
    <h3>キャストアイコン</h3>
    @foreach($icon_items as $icon_item)
    <label>
        <input type="checkbox" name="icon_items[]" value="{{ $icon_item->id }}"
        {{ in_array($icon_item->id, $selectedIconItems) ? 'checked' : '' }}
        class="iconItem-checkbox" data-iconitem-id="{{ $icon_item->id }}">
        <p>{{ $icon_item->title }}</p>

        @if($icon_item->filename)
        <p class="img"><img src="{{ asset('uploads/icon_item/' . $icon_item->filename) }}" alt=""></p>
    @endif
    </label>
    @endforeach
</section>
@endif
--}}


</div>


<script>
    $(document).ready(function() {
        // オプションのチェックボックスに変更があった場合にAjaxで反映
        $('.option-checkbox').on('change', function() {
            var optionId = $(this).data('option-id');
            var isChecked = $(this).is(':checked');
            var itemId = {{ $item->id }};

            console.log(optionId);
            $.ajax({
                url: '{{ route("item.items.updateOption", ["id" => $item->id]) }}',
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

    $(document).ready(function() {
        // オプションのチェックボックスに変更があった場合にAjaxで反映
        $('.option-pay-checkbox').on('change', function() {
            var optionPayId = $(this).data('option-pay-id');
            var isCheckedPay = $(this).is(':checked');
            var itemId = {{ $item->id }};

            $.ajax({
                url: '{{ route("item.items.updateOptionPay", ["id" => $item->id]) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    option_pay_id: optionPayId,
                    pay_checked: isCheckedPay
                },
                success: function(response) {
                    if (response.success) {
                        console.log('オプションPAYが更新されました');
                    }
                },
                error: function(xhr) {
                    console.log('エラーが発生しました');
                }
            });
        });
    });


    $(document).ready(function() {
        // キャストアイコンのチェックボックスに変更があった場合にAjaxで反映
        $('.iconItem-checkbox').on('change', function() {
            var iconItem_id = $(this).data('iconitem-id');
            var isCheckedIconItem = $(this).is(':checked');
            var itemId = {{ $item->id }};

            $.ajax({
                url: '{{ route("item.items.updateIconItem", ["id" => $item->id]) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    iconItem_id: iconItem_id,
                    iconItem_checked: isCheckedIconItem
                },
                success: function(response) {
                    if (response.success) {
                        console.log('キャストアイコンが更新されました');
                    }
                },
                error: function(xhr) {
                    console.log('エラーが発生しました');
                }
            });
        });
    });

    // コース設定更新
    function updateCourseStatus(itemId, courseId, status) {
        $.ajax({
            url: '{{ route("item.items.updateCourse", ["id" => $item->id]) }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                course_id: courseId,
                status: status
            },
            success: function(response) {
                if (response.success) {
                    // ボタンの状態を更新
                    $('.status-btn[data-course-id="' + courseId + '"]').removeClass('active');
                    $('.status-btn[data-status="' + status + '"][data-course-id="' + courseId + '"]').addClass('active');
                    console.log('コース設定が更新されました');
                }
            },
            error: function(xhr) {
                console.log('エラーが発生しました');
            }
        });
    }

    // 料金設定更新
    $(document).ready(function() {
        // 料金入力フィールドの変更を監視
        $('.fee-input').on('blur', function() {
            var courseId = $(this).data('course-id');
            var feeType = $(this).data('fee-type');
            var value = $(this).val();

            updateCourseFee(courseId, feeType, value);
        });

        // 一律料金チェックボックスの変更を監視
        $('.uniform-rate-checkbox').on('change', function() {
            var courseId = $(this).data('course-id');
            var isChecked = $(this).is(':checked');
            var uniformRateInput = $('.uniform-rate-input[data-course-id="' + courseId + '"]');
            var feeInputs = $('.fee-input[data-course-id="' + courseId + '"]');

            console.log('一律料金チェックボックス変更:', courseId, isChecked);

            if (isChecked && uniformRateInput.val()) {
                var uniformRate = parseInt(uniformRateInput.val());

                // 30分単位で料金を自動計算
                feeInputs.each(function() {
                    var feeType = $(this).data('fee-type');
                    var calculatedFee = 0;

                    switch(feeType) {
                        case 'fee_60':
                            calculatedFee = uniformRate * 2; // 60分 = 30分 × 2
                            break;
                        case 'fee_90':
                            calculatedFee = uniformRate * 3; // 90分 = 30分 × 3
                            break;
                        case 'fee_120':
                            calculatedFee = uniformRate * 4; // 120分 = 30分 × 4
                            break;
                        case 'fee_150':
                            calculatedFee = uniformRate * 5; // 150分 = 30分 × 5
                            break;
                        case 'fee_180':
                            calculatedFee = uniformRate * 6; // 180分 = 30分 × 6
                            break;
                        case 'fee_extension':
                            calculatedFee = uniformRate; // 延長30分 = 30分 × 1
                            break;
                    }

                    $(this).val(calculatedFee);
                });

                // 料金設定を更新
                var feeData = {
                    fee_60: uniformRate * 2,
                    fee_90: uniformRate * 3,
                    fee_120: uniformRate * 4,
                    fee_150: uniformRate * 5,
                    fee_180: uniformRate * 6,
                    fee_extension: uniformRate,
                    uniform_rate: uniformRate,
                    use_uniform_rate: 'true'
                };

                console.log('一律料金計算結果:', feeData);
                updateCourseFeeWithData(courseId, feeData);
            } else {
                // チェックを外した場合は料金をクリア
                feeInputs.each(function() {
                    $(this).val('');
                });

                // 料金設定を更新
                var feeData = {
                    fee_60: '',
                    fee_90: '',
                    fee_120: '',
                    fee_150: '',
                    fee_180: '',
                    fee_extension: '',
                    uniform_rate: uniformRateInput.val(),
                    use_uniform_rate: 'false'
                };

                console.log('料金クリア結果:', feeData);
                updateCourseFeeWithData(courseId, feeData);
            }
        });

        // 一律料金入力フィールドの変更を監視
        $('.uniform-rate-input').on('input', function() {
            var courseId = $(this).data('course-id');
            var value = $(this).val();
            var checkbox = $('.uniform-rate-checkbox[data-course-id="' + courseId + '"]');

            if (checkbox.is(':checked') && value) {
                var uniformRate = parseInt(value);

                // 料金を再計算
                var feeInputs = $('.fee-input[data-course-id="' + courseId + '"]');
                feeInputs.each(function() {
                    var feeType = $(this).data('fee-type');
                    var calculatedFee = 0;

                    switch(feeType) {
                        case 'fee_60':
                            calculatedFee = uniformRate * 2;
                            break;
                        case 'fee_90':
                            calculatedFee = uniformRate * 3;
                            break;
                        case 'fee_120':
                            calculatedFee = uniformRate * 4;
                            break;
                        case 'fee_150':
                            calculatedFee = uniformRate * 5;
                            break;
                        case 'fee_180':
                            calculatedFee = uniformRate * 6;
                            break;
                        case 'fee_extension':
                            calculatedFee = uniformRate;
                            break;
                    }

                    $(this).val(calculatedFee);
                });

                // 料金設定を更新
                var feeData = {
                    fee_60: uniformRate * 2,
                    fee_90: uniformRate * 3,
                    fee_120: uniformRate * 4,
                    fee_150: uniformRate * 5,
                    fee_180: uniformRate * 6,
                    fee_extension: uniformRate,
                    uniform_rate: uniformRate,
                    use_uniform_rate: 'true'
                };

                updateCourseFeeWithData(courseId, feeData);
            }
        });
    });

    function updateCourseFee(courseId, feeType, value) {
        // 現在の料金設定を取得
        var currentData = getCurrentFeeData(courseId);
        currentData[feeType] = value;

        $.ajax({
            url: '{{ route("item.items.updateCourseFee", ["id" => $item->id]) }}',
            method: 'POST',
            data: currentData,
            success: function(response) {
                if (response.success) {
                    console.log('料金設定が更新されました');
                }
            },
            error: function(xhr) {
                console.log('エラーが発生しました');
                console.log(xhr.responseText);
            }
        });
    }

    function updateCourseFeeWithData(courseId, feeData) {
        $.ajax({
            url: '{{ route("item.items.updateCourseFee", ["id" => $item->id]) }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                course_id: courseId,
                ...feeData
            },
            success: function(response) {
                if (response.success) {
                    console.log('料金設定が更新されました');
                }
            },
            error: function(xhr) {
                console.log('エラーが発生しました');
                console.log(xhr.responseText);
            }
        });
    }

    function getCurrentFeeData(courseId) {
        var data = {
            _token: '{{ csrf_token() }}',
            course_id: courseId
        };

        // 現在の入力値を取得
        $('.fee-input[data-course-id="' + courseId + '"]').each(function() {
            var feeType = $(this).data('fee-type');
            var value = $(this).val();
            data[feeType] = value;
        });

        // 一律料金設定を取得
        var uniformRateCheckbox = $('.uniform-rate-checkbox[data-course-id="' + courseId + '"]');
        var uniformRateInput = $('.uniform-rate-input[data-course-id="' + courseId + '"]');

        data['use_uniform_rate'] = uniformRateCheckbox.is(':checked') ? 'true' : 'false';
        data['uniform_rate'] = uniformRateInput.val();

        console.log('取得したデータ:', data);
        return data;
    }



    // $(document).ready(function() {
    //     // プロフィールのチェックボックスに変更があった場合にAjaxで反映
    //     $('.profile-text').on('change', function() {
    //         var profile_id = $(this).data('profile-id');
    //         var isCheckedProfile = $(this).is(':checked');
    //         var itemId = {{ $item->id }};

    //         $.ajax({
    //             url: '{{ route("item.items.updateProfile", ["id" => $item->id]) }}',
    //             method: 'POST',
    //             data: {
    //                 _token: '{{ csrf_token() }}',
    //                 profile_id: profile_id,
    //                 profile_checked: isCheckedProfile
    //             },
    //             success: function(response) {
    //                 if (response.success) {
    //                     console.log('プロフィールが更新されました');
    //                 }
    //             },
    //             error: function(xhr) {
    //                 console.log('エラーが発生しました');
    //             }
    //         });
    //     });
    // });


</script>

<script src="{{ asset('js/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('js/admin-tinymce.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initAdminTinyMCE(
            '#item_comment2',
            '{{ asset("js/tinymce/skins/ui/oxide") }}',
            '{{ asset("js/tinymce/skins/content/default/content.min.css") }}',
            { height: 320 }
        );

        // フォーム送信時にTinyMCEの内容をtextareaに同期
        var comment2El = document.getElementById('item_comment2');
        if (comment2El && comment2El.form && typeof tinymce !== 'undefined') {
            comment2El.form.addEventListener('submit', function() {
                var editor = tinymce.get('item_comment2');
                if (editor) {
                    editor.save();
                }
            });
        }
    });
</script>

<style>
/* コース設定のスタイル */
.course-settings {
    margin-bottom: 20px;
}

.course-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.25rem 0.5rem;
    border: 1px solid #e2e8f0;
    margin-bottom: 0.2rem;
    border-radius: 8px;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.course-name {
    font-weight: 600;
    flex: 1;
    color: #374151;
    font-size: 14px;
}

.course-status {
    display: flex;
    gap: 8px;
}

.status-btn {
    padding: 8px 16px;
    border: 1px solid #d1d5db;
    background: #f9fafb;
    cursor: pointer;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.2s ease;
    color: #6b7280;
}

.status-btn:hover {
    background: #f3f4f6;
    border-color: #9ca3af;
}

.status-btn.active {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
    box-shadow: 0 1px 2px rgba(59, 130, 246, 0.3);
}

/* 料金設定のスタイル */
.fee-settings {
    margin-top: 20px;
}

.course-fee-item {
    margin-bottom: 30px;
    padding: 20px;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    background-color: #fafafa;
}

.course-name {
    font-size: 14px;
    font-weight: bold;
    color: #333;
    margin-bottom: 15px;
}

.uniform-rate-section {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    margin-bottom: 15px;
}

.fee-table {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    width: 100%;
}

.fee-table table {
    max-width: 100%;
    min-width: 680px;
    border-collapse: collapse;
    white-space: nowrap;
    table-layout: fixed;
}

.fee-table th,
.fee-table td {
    display: table-cell;
    width: calc(100% / 7);
    padding: 8px;
    text-align: center;
    border: 1px solid #ddd;
    min-width: 80px;
    white-space: nowrap;
    box-sizing: border-box;
    /* overflow: hidden; */
    /* text-overflow: ellipsis; */
}

.fee-table th {
    background: #f8f9fa;
    font-weight: bold;
    position: sticky;
    top: 0;
    z-index: 10;
}

.fee-table td:first-child {
    min-width: 120px;
    text-align: left;
}

.fee-input {
    width: 80px;
    padding: 5px;
    border: 1px solid #ddd;
    border-radius: 3px;
    text-align: center;
}

.fee-input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

.uniform-rate-input {
    width: 100px;
    padding: 5px;
    border: 1px solid #ddd;
    border-radius: 3px;
    text-align: center;
}

.uniform-rate-input:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}

/* タブのスタイル */
.tab-wrap {
    margin-top: 20px;
}

.tab-switch {
    display: none;
}

.tab-label {
    display: inline-block;
    min-width: calc(100% / 5);
    padding: 10px 5px;
    background: #f5f5f5;
    border: 1px solid #ddd;
    cursor: pointer;
    margin-right: 5px;
    border-radius: 5px 5px 0 0;
    font-size: 0.7rem;
    text-align: center;
}

.tab-switch:checked + .tab-label {
    background: #3b82f6;
    color: #333;
    border-color: #ddd;
}

.tab-content {
    display: none;
    padding: 20px;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 5px 5px;
}

.tab-switch:checked + .tab-label + .tab-content {
    display: block;
}

/* フォームのスタイル */
.editSection {
    margin-bottom: 30px;
}

.editSection h3 {
    margin-bottom: 15px;
    color: #333;
    border-bottom: 2px solid #007bff;
    padding-bottom: 5px;
}

/* ボタンのスタイル */
.btn {
    padding: 10px 20px;
    background: #007bff;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
}

.btn:hover {
    background: #0056b3;
}

.size_m {
    padding: 8px 16px;
    font-size: 12px;
}

/* テーブルのスタイル */
table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

th, td {
    padding: 5px;
    border: 1px solid #ddd;
    text-align: left;
}

th {
    background: #f8f9fa;
    font-weight: bold;
}

/* 入力フィールドのスタイル */
input[type="text"],
input[type="password"],
input[type="email"],
select,
textarea {
    width: 100%;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 3px;
    font-size: 14px;
    box-sizing: border-box;
}

textarea {
    height: 100px;
    resize: vertical;
}

/* チェックボックスとラジオボタンのスタイル */
input[type="checkbox"],
input[type="radio"] {
    margin-right: 5px;
}

/* アラートのスタイル */
.alert {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 5px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

</style>

</x-item-layout>
