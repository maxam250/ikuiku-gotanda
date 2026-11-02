<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MigrateOldPickups extends Command
{
    protected $signature = 'migrate:old-pickups';
    protected $description = '旧DBから新DBへピックアップ情報を移行する (pickup_data→pickups)';

    public function handle()
    {
        $oldPickups = DB::connection('old_mysql')
            ->table('pickup_data')
            ->orderBy('pickup_id')
            ->get();

        foreach ($oldPickups as $old) {
            if (empty($old->pickup_item_id)) continue;

            $date = (is_numeric($old->pickup_sort) && $old->pickup_sort > 1000000000)
                ? Carbon::createFromTimestamp($old->pickup_sort)
                : now();

            $data = [
                'item_id'    => $old->pickup_item_id,
                'sort'       => is_numeric($old->pickup_sort) ? (int) $old->pickup_sort : 0,
                'date'       => $date,
                'updated_at' => now(),
            ];

            if (DB::table('pickups')->where('id', $old->pickup_id)->exists()) {
                DB::table('pickups')->where('id', $old->pickup_id)->update($data);
            } else {
                DB::table('pickups')->insert(array_merge(
                    ['id' => $old->pickup_id, 'created_at' => now()],
                    $data
                ));
            }
        }

        $this->info('pickups 移行完了');
    }
}
