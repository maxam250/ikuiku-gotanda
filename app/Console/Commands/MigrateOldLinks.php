<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MigrateOldLinks extends Command
{
    protected $signature = 'migrate:old-links';
    protected $description = '旧DBから新DBへリンク情報を移行する (cms_link_data→links)';

    public function handle()
    {
        $oldLinks = DB::connection('old_mysql')
            ->table('cms_link_data')
            ->orderBy('item_id')
            ->get();

        foreach ($oldLinks as $old) {
            $active = ($old->item_active == 0) ? 0 : 1;

            // item_size: 0=88px → position 3(88x31), 1=200px → position 2(200x40)
            $position = isset($old->item_size) ? ($old->item_size == 1 ? 2 : 3) : ($old->item_position ?? 3);

            $data = [
                'title'      => $old->item_name ?? '',
                'active'     => (string) $active,
                'fullcode'   => $old->item_point ?: null,
                'link'       => $old->item_url ?: null,
                'sort'       => $old->item_sort ?? 0,
                'filename'   => $old->item_filename ?: null,
                'position'   => $position,
                'updated_at' => now(),
            ];

            if (DB::table('links')->where('id', $old->item_id)->exists()) {
                DB::table('links')->where('id', $old->item_id)->update($data);
            } else {
                DB::table('links')->insert(array_merge(
                    ['id' => $old->item_id, 'created_at' => Carbon::parse($old->item_regist)],
                    $data
                ));
            }
        }

        $this->info('links 移行完了');
    }
}
