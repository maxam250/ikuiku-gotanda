<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;


use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;


//$encrypted = Crypt::encryptString('平文の値');
//$decrypted = Crypt::decryptString($encrypted);


class MigrateOldSchedules extends Command
{
    protected $signature = 'migrate:old-schedules';
    protected $description = '旧DBから新DBへスケジュール情報を移行する';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $oldGalleries = DB::connection('old_mysql')->table('sch_data')->get();

        $yesterday = Carbon::yesterday()->toDateString();

        // item_idを旧idからマッピング
        // ※migrate:old-items で旧IDのまま移行している場合は空にする
        $mapping = [];


        $oldGalleries = DB::connection('old_mysql')
            ->table('cms_sch_data')
            ->whereDate('sch_date', '>=', $yesterday)
            ->orderBy('sch_id')
            ->get();

        foreach ($oldGalleries as $old) {
            $itemId = $mapping[$old->sch_item_id] ?? $old->sch_item_id;

            DB::table('schedules')->insert([
                'item_id' => $itemId,
                'date' => $old->sch_date,
                'time_start' => $old->sch_time_text1,
                'time_end' => $old->sch_time_text2,
                'ts_start' => $old->sch_flag,
                'ts_end' => $old->sch_unix_time,
                'status' => $old->sch_status,
                'sort' => 0,
                'flag' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        }

        $this->info('移行完了');
    }

}
