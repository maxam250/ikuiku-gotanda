<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MigrateOldCostumes extends Command
{
    protected $signature = 'migrate:old-costumes';
    protected $description = '旧DBから新DBへコスチューム情報を移行する (cms_costume_data→costumes)';

    public function handle()
    {
        $oldCostumes = DB::connection('old_mysql')
            ->table('cms_costume_data')
            ->orderBy('item_id')
            ->get();

        foreach ($oldCostumes as $old) {
            // item_active: 1=表示 → 1, 2=非表示 → 0
            $active = ($old->item_active == 1) ? 1 : 0;

            $data = [
                'active'     => $active,
                'name'       => $old->item_name ?? '',
                'body'       => $old->item_point ?: null,
                'cate'       => 0,
                'sort'       => $old->item_sort ?? 0,
                'filename'   => $old->item_filename ?: null,
                'updated_at' => now(),
            ];

            if (DB::table('costumes')->where('id', $old->item_id)->exists()) {
                DB::table('costumes')->where('id', $old->item_id)->update($data);
            } else {
                DB::table('costumes')->insert(array_merge(
                    ['id' => $old->item_id, 'created_at' => Carbon::parse($old->item_regist)],
                    $data
                ));
            }
        }

        $this->info('costumes 移行完了');
    }
}
