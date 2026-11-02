{{-- ハニーポット（スパムボット検出用の隠しフィールド） --}}
<div style="position: absolute; left: -9999px; visibility: hidden;" aria-hidden="true">
    <label for="website">Website (leave blank)</label>
    <input type="text" name="website" id="website" value="" autocomplete="off" tabindex="-1">

    <label for="url">URL (leave blank)</label>
    <input type="text" name="url" id="url" value="" autocomplete="off" tabindex="-1">

    <label for="phone_number">Phone (leave blank)</label>
    <input type="text" name="phone_number" id="phone_number" value="" autocomplete="off" tabindex="-1">
</div>

{{-- フォーム送信時間チェック用 --}}
<input type="hidden" name="form_time" value="{{ time() }}">
