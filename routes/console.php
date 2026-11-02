<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// バックアップスケジュール（毎日02:00に実行）
Schedule::command('backup:run')->dailyAt('02:00');

// メール返信取り込み（Maildir 経由の場合、cron で毎分実行する例）
// * * * * * /usr/local/bin/php /path/to/castup/artisan mail:fetch-replies
