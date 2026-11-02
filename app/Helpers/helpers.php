<?php

use Carbon\Carbon;



function generateTimeOptions($start, $end)
{
    $times = [];
    $startTime = convertToTimestamp($start);
    $endTime = convertToTimestamp($end);

    // 開始時間または終了時間が無効な場合は空の配列を返す
    if ($startTime === 0 || $endTime === 0) {
        return $times;
    }

    while ($startTime <= $endTime) {
        $hours = intdiv($startTime, 3600);  // 時間を取得
        $minutes = ($startTime % 3600) / 60;  // 分を取得

        // 24時間を超える場合に対応する
        $displayHours = ($hours >= 24) ? $hours - 24 : $hours;

        $timeLabel = sprintf('%02d:%02d', $displayHours, $minutes);
        $timeValue = sprintf('%02d:%02d:00', $hours, $minutes); // データベースに合わせたフォーマット

        // 時間のオプションを配列に追加 (表示用ラベルと値)
        $times[$timeValue] = $timeLabel;

        // 30分刻みで時間を追加
        $startTime += 1800;  // 1800秒 = 30分
    }

    return $times;
}

/**
 * 予約フォームのコース料金用 / 分単位のオプションを生成
 *
 * @param int $start 開始分数
 * @param int $end 終了分数
 * @param int $step ステップ値
 * @return array 分単位のオプション
 */
function generateMinuteOptions($start = 60, $end = 480, $step = 30)
{
    $options = [];
    for ($i = $start; $i <= $end; $i += $step) {
        $options[$i] = "{$i}分";
    }
    return $options;
}

// app/Helpers/TimeHelper.php
function generateTimeOptions2222($start, $end)
{
    $times = [];
    $startTime = convertToTimestamp($start);
    $endTime = convertToTimestamp($end);

    while ($startTime <= $endTime) {
        $hours = intdiv($startTime, 3600);  // 時間を取得
        $minutes = ($startTime % 3600) / 60;  // 分を取得

        // 24時間を超える場合に対応する
        $hoursFormatted = ($hours >= 24) ? $hours - 24 : $hours;
        $timeLabel = sprintf('%02d:%02d', $hoursFormatted, $minutes);

        // 24時間以上の時間をそのまま保持
        $times[sprintf('%02d:%02d:00', $hours, $minutes)] = $timeLabel;

        // 30分刻みで時間を追加
        $startTime += 1800;  // 1800秒 = 30分
    }

    return $times;
}

/**
 * 指定した時間を分解する
 *
 * @param time $time
 * @return array
 */

function convertToTimestamp($time)
{
    // 時間が24:00以上の場合を考慮する
    $timeParts = explode(':', $time);
    
    // 時間の形式が正しくない場合はデフォルト値を返す
    if (count($timeParts) < 2) {
        return 0;
    }
    
    $hours = (int)$timeParts[0];
    $minutes = (int)$timeParts[1];

    // 24時間以上の時間をそのまま加算
    return ($hours * 3600) + ($minutes * 60);
}


/////////////////////////////////////////////////////////////////////

if (!function_exists('formatScheduleTime')) {
    /**
     * 24時超のスケジュール時刻を表示用文字列に変換
     * '28:00:00' → '翌04:00'  /  '22:30:00' → '22:30'
     */
    function formatScheduleTime(?string $time): string
    {
        if (!$time) return '';
        $parts = explode(':', $time);
        $h = (int)($parts[0] ?? 0);
        $m = (int)($parts[1] ?? 0);
        if ($h >= 24) {
            return sprintf('翌%02d:%02d', $h - 24, $m);
        }
        return sprintf('%02d:%02d', $h, $m);
    }
}

if (!function_exists('scheduleTimeToFloat')) {
    /**
     * 'HH:MM:SS' → 時間の小数表現 (タイムライン位置計算用)
     * '28:30:00' → 28.5
     */
    function scheduleTimeToFloat(?string $time): float
    {
        if (!$time) return 0.0;
        $parts = explode(':', $time);
        $h = (int)($parts[0] ?? 0);
        $m = (int)($parts[1] ?? 0);
        return $h + ($m / 60);
    }
}

if (!function_exists('adjustTimeWithShift')) {
    /**
     * 設定された切り替わり時間を使って当日の日付を返す
     * DBのsettings.schedule_switch_hourを優先し、なければconfig('custom.time_shift',6)にフォールバック
     *
     * @return string 調整された日付 (Y-m-d形式)
     */
    function adjustTimeWithShift()
    {
        $setting = \App\Models\Setting::first();
        $switchHour = $setting?->schedule_switch_hour ?? config('custom.time_shift', 6);

        $now = Carbon::now();

        if ($now->hour < $switchHour) {
            return $now->subDay()->format('Y-m-d');
        }

        return $now->format('Y-m-d');
    }
}


/////////////////////////////////////////////////////////////////////

/**
 * 1週間分の日付リストを生成
 *
 * @return string 調整された日付 (Y-m-d形式)
 */
if (!function_exists('getWeekDatesJP')) {
    /**
     * 現在の日付から1週間分の日本語形式の日付リストを返すヘルパー関数
     *
     * @param int $weekCount 何週間分取得するか（デフォルト1週間）
     * @return array
     */
    function getWeekDatesJP($weekCount = 1) {
        // 日本語ロケールを設定
        Carbon::setLocale('ja');

        // schedule_switch_hourで調整した当日を起点にする
        $startOfWeek = Carbon::parse(adjustTimeWithShift())->startOfDay();
        $endOfWeek = $startOfWeek->copy()->addWeeks($weekCount);
        $dates = [];

        // 日付を取得し、日本語フォーマットに変換して配列に追加
        for ($date = $startOfWeek; $date->lte($endOfWeek); $date->addDay()) {
            $dates[] = $date->isoFormat('YYYY年MM月DD日（ddd）');
        }

        return $dates;
    }
}



if (!function_exists('getWeekDates')) {
    /**
     * 現在の日付から1週間分の日付リストを取得し、リンク用と表示用に分けて返す
     *
     * @param int $weekCount 何週間分取得するか（デフォルト1週間）
     * @return array
     */
    function getWeekDates($weekCount = 1) {
        // 日本語ロケールを設定
        Carbon::setLocale('ja');

        // schedule_switch_hourで調整した当日を起点にする
        $startOfWeek = Carbon::parse(adjustTimeWithShift())->startOfDay();
        $endOfWeek = $startOfWeek->copy()->addWeeks($weekCount);
        $dates = [];

        // 日付を取得し、リンク用と表示用のフォーマットを別々に追加
        for ($date = $startOfWeek; $date->lte($endOfWeek); $date->addDay()) {
            $dates[] = [
                'date' => $date->format('Y-m-d'),               // リンク用のYYYY-mm-dd形式
                // 'date_jp' => $date->isoFormat('YYYY年MM月DD日（dddd）') // 日本語表示用
                'date_jp' => $date->isoFormat('MM月DD日（dd）') // 日本語表示用
            ];
        }

        return $dates;
    }
}
/////////////////////////////////////////////////////////////////////


/**
 * 指定した月のカレンダーを生成する
 *
 * @param int $year
 * @param int $month
 * @return array
 */
function generateCalendar($year, $month)
{
    $weeks = [];
    $date = Carbon::createFromDate($year, $month, 1);

    // 1日の曜日 (0:日曜日, 1:月曜日, ..., 6:土曜日)
    $firstDayOfWeek = $date->dayOfWeek;

    // カレンダーの最初の週のための空白の配列を用意
    $week = array_fill(0, $firstDayOfWeek, null);

    // 月の日数を取得
    $daysInMonth = $date->daysInMonth;

    // 各日の情報を生成
    for ($day = 1; $day <= $daysInMonth; $day++, $firstDayOfWeek++) {
        $week[$firstDayOfWeek] = $day;

        // 土曜日（週の終わり）または月の最終日であれば、週を追加
        if ($firstDayOfWeek % 7 == 6 || $day == $daysInMonth) {
            $weeks[] = $week;
            $week = []; // 新しい週のためにリセット
        }
    }

    return $weeks;
}


/////////////////////////////////////////////////////////////////////
/**
 * テキストに含まれるhttp:をAutoLink
 *
 * @param int $text
 * @return array
 */
// if (!function_exists('autolink')) {
//     function autolink($text)
//     {
//         $escaped = e($text);
//         $linked = preg_replace(
//             '/(https?:\/\/[\w\-\.\/?&=#]+)/u',
//             '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
//             $escaped
//         );
//         return nl2br($linked);
//     }
// }

/////////////////////////////////////////////////////////////////////
/**
 * エスケープ処理のヘルパー関数
 * kirari-projectで実装されたエスケープ処理を参考
 */

if (!function_exists('h')) {
    /**
     * HTMLエスケープ処理
     * htmlspecialcharsのラッパー関数
     *
     * @param string|null $value エスケープする値
     * @param int $flags htmlspecialcharsのフラグ
     * @param string $encoding エンコーディング
     * @return string エスケープされた文字列
     */
    function h($value, $flags = ENT_QUOTES, $encoding = 'UTF-8')
    {
        if (is_null($value)) {
            return '';
        }
        
        if (is_array($value)) {
            return array_map(function($item) use ($flags, $encoding) {
                return h($item, $flags, $encoding);
            }, $value);
        }
        
        return htmlspecialchars((string)$value, $flags, $encoding);
    }
}

if (!function_exists('e_js')) {
    /**
     * JavaScriptエスケープ処理
     * JavaScript内で使用する文字列を安全にエスケープ
     *
     * @param string|null $value エスケープする値
     * @return string エスケープされた文字列
     */
    function e_js($value)
    {
        if (is_null($value)) {
            return '';
        }
        
        // JSONエンコードを使用してJavaScriptエスケープ
        return json_encode((string)$value, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
    }
}

if (!function_exists('e_url')) {
    /**
     * URLエスケープ処理
     * URLパラメータとして使用する文字列を安全にエスケープ
     *
     * @param string|null $value エスケープする値
     * @return string エスケープされた文字列
     */
    function e_url($value)
    {
        if (is_null($value)) {
            return '';
        }
        
        return urlencode((string)$value);
    }
}

if (!function_exists('e_attr')) {
    /**
     * HTML属性エスケープ処理
     * HTML属性値として使用する文字列を安全にエスケープ
     *
     * @param string|null $value エスケープする値
     * @return string エスケープされた文字列
     */
    function e_attr($value)
    {
        if (is_null($value)) {
            return '';
        }
        
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8', false);
    }
}

if (!function_exists('sanitize_html')) {
    /**
     * HTMLサニタイズ処理
     * 許可されたタグのみを残してHTMLをサニタイズ
     *
     * @param string|null $value サニタイズする値
     * @param array $allowedTags 許可するHTMLタグの配列
     * @return string サニタイズされた文字列
     */
    function sanitize_html($value, $allowedTags = [])
    {
        if (is_null($value)) {
            return '';
        }
        
        // 許可されたタグが指定されている場合はstrip_tagsを使用
        if (!empty($allowedTags)) {
            $allowedTagsString = '<' . implode('><', $allowedTags) . '>';
            return strip_tags((string)$value, $allowedTagsString);
        }
        
        // 許可されたタグがない場合は全てのHTMLタグを削除
        return strip_tags((string)$value);
    }
}

if (!function_exists('escape_mixed')) {
    /**
     * 混合コンテキスト用エスケープ処理
     * HTMLとJavaScriptが混在する場合のエスケープ
     *
     * @param string|null $value エスケープする値
     * @return string エスケープされた文字列
     */
    function escape_mixed($value)
    {
        if (is_null($value)) {
            return '';
        }
        
        // まずHTMLエスケープ
        $htmlEscaped = h($value);
        
        // JavaScript内で使用する場合は追加のエスケープが必要な場合がある
        // ここでは基本的にHTMLエスケープを返す
        return $htmlEscaped;
    }
}

if (!function_exists('is_ezweb_email')) {
    /**
     * メールアドレスが au EZweb（メール返信の In-Reply-To が付かない環境向けの案内表示用）。
     */
    function is_ezweb_email(?string $email): bool
    {
        if ($email === null || $email === '') {
            return false;
        }

        return str_ends_with(strtolower(trim($email)), '@ezweb.ne.jp');
    }
}
