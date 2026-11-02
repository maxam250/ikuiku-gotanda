<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class MigrateOldNewfaces extends Command
{
    protected $signature = 'migrate:old-newfaces';
    protected $description = '旧DBから新DBへ新人情報を移行する (newface_data→newfaces)';

    public function handle()
    {
        // ※店舗によっては newface_data が存在しないためスキップ
        // ※newface_data が無い店舗（鶯谷など）は旧 common.php と同じ条件で item_data から作る
        //   item_active = 1 AND item_new = 1 ORDER BY item_entry_date DESC LIMIT 10
        if (!Schema::connection('old_mysql')->hasTable('newface_data')) {
            $this->warn('newface_data が存在しないため item_data（item_new=1）から作成');
            $this->fromItemData();
            return;
        }

        // 旧 top.php / newface.php と同じ並び（pickup_sort 昇順 → item_id 降順）を sort の連番にする
        // 入店日は item_data.item_entry_date（newface_data 側の日付は未使用で 0000-00-00 のため）
        $oldNewfaces = DB::connection('old_mysql')
            ->table('newface_data')
            ->leftJoin('item_data', 'item_data.item_id', '=', 'newface_data.pickup_item_id')
            ->orderByRaw('CAST(newface_data.pickup_sort AS UNSIGNED) ASC')
            ->orderByDesc('newface_data.pickup_item_id')
            ->get(['newface_data.*', 'item_data.item_entry_date']);

        foreach ($oldNewfaces->values() as $i => $old) {
            if (empty($old->pickup_item_id)) continue;

            $entry = $old->item_entry_date ?? null;
            if ($entry && !str_starts_with($entry, '0000')) {
                $date = Carbon::parse($entry);
            } elseif (is_numeric($old->pickup_sort) && $old->pickup_sort > 1000000000) {
                $date = Carbon::createFromTimestamp($old->pickup_sort);
            } else {
                $date = now();
            }

            $data = [
                'item_id'    => $old->pickup_item_id,
                'sort'       => $i,
                'date'       => $date,
                'updated_at' => now(),
            ];

            if (DB::table('newfaces')->where('id', $old->pickup_id)->exists()) {
                DB::table('newfaces')->where('id', $old->pickup_id)->update($data);
            } else {
                DB::table('newfaces')->insert(array_merge(
                    ['id' => $old->pickup_id, 'created_at' => now()],
                    $data
                ));
            }
        }

        $this->info('newfaces 移行完了');
    }

    private function fromItemData(): void
    {
        $olds = DB::connection('old_mysql')
            ->table('item_data')
            ->where('item_active', 1)
            ->where('item_new', 1)
            ->orderByDesc('item_entry_date')
            ->limit(10)
            ->get(['item_id', 'item_entry_date']);

        foreach ($olds->values() as $i => $old) {
            $date = $old->item_entry_date && $old->item_entry_date !== '0000-00-00 00:00:00'
                ? Carbon::parse($old->item_entry_date)
                : now();

            DB::table('newfaces')->updateOrInsert(
                ['item_id' => $old->item_id],
                ['sort' => $i, 'date' => $date, 'created_at' => now(), 'updated_at' => now()]
            );
        }

        $this->info('newfaces 作成完了 (' . $olds->count() . '件)');
    }
}
