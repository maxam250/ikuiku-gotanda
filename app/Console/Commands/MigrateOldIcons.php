<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateOldIcons extends Command
{
    protected $signature = 'migrate:old-icons';
    protected $description = '旧DBから新DBへキャストアイコン情報を移行する (item_icon→icon_items)';

    public function handle()
    {
        $oldIcons = DB::connection('old_mysql')
            ->table('item_icon')
            ->orderBy('cate_mid_id')
            ->get();

        foreach ($oldIcons as $old) {
            $data = [
                'title'      => $old->cate_mid_name ?? '',
                'filename'   => $old->item_icon_img ?: null,
                'sort'       => is_numeric($old->cate_mid_sort) ? (int) $old->cate_mid_sort : 0,
                'updated_at' => now(),
            ];

            if (DB::table('icon_items')->where('id', $old->cate_mid_id)->exists()) {
                DB::table('icon_items')->where('id', $old->cate_mid_id)->update($data);
            } else {
                DB::table('icon_items')->insert(array_merge(
                    ['id' => $old->cate_mid_id, 'created_at' => now()],
                    $data
                ));
            }
        }

        $this->info('icon_items 移行完了');
    }
}
