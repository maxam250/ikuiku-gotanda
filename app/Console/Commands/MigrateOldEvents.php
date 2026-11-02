<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MigrateOldEvents extends Command
{
    protected $signature = 'migrate:old-events';
    protected $description = '旧DBから新DBへイベント情報を移行する (cms_events_data→events)';

    public function handle()
    {
        $oldEvents = DB::connection('old_mysql')
            ->table('cms_events_data')
            ->orderBy('item_id')
            ->get();

        foreach ($oldEvents as $old) {
            // item_active: 0=非表示, 1=TOP only, 2=event page only hidden, 3=全表示 → 0=非表示, 1=表示
            $active = ($old->item_active == 0) ? 0 : 1;

            // 表示位置：旧 item_block_no（0=TOP&イベント, 1=TOPのみ, 2=イベントのみ）
            //   → 新 position（0=表示なし, 1=TOP&イベント, 2=TOPのみ, 3=イベントのみ）。非公開は表示なし
            $position = $active ? ((int) ($old->item_block_no ?? 0) + 1) : 0;

            $data = [
                'active'      => $active,
                'title'       => $old->item_name ?? '',
                'body'        => $old->item_text ?? null,
                'sort'        => $old->item_sort_shinjuku ?? $old->item_sort ?? 0,
                'filename'    => $old->item_filename ?: null,
                'position'    => $position,
                'cate'        => 0,
                'link'        => $old->item_url ?: null,
                'timer_flag'  => 0,
                'date_start'  => null,
                'date_end'    => null,
                'updated_at'  => now(),
            ];

            if (DB::table('events')->where('id', $old->item_id)->exists()) {
                DB::table('events')->where('id', $old->item_id)->update($data);
            } else {
                DB::table('events')->insert(array_merge(
                    ['id' => $old->item_id, 'created_at' => Carbon::parse($old->item_regist)],
                    $data
                ));
            }
        }

        $this->info('events 移行完了');
    }
}
