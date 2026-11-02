<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateOldItemImages extends Command
{
    protected $signature = 'migrate:old-item-images';
    protected $description = '旧DBから新DBへキャスト画像情報を移行する (item_img_data→item_images)';

    public function handle()
    {
        $oldImages = DB::connection('old_mysql')
            ->table('item_img_data')
            ->whereNotNull('img_i_id')
            ->orderBy('img_id')
            ->get();

        // item_idごとに一度クリアしてから再挿入
        $processedItems = [];

        foreach ($oldImages as $old) {
            $itemId = $old->img_i_id;

            if (!in_array($itemId, $processedItems)) {
                DB::table('item_images')->where('item_id', $itemId)->delete();
                $processedItems[] = $itemId;
            }

            if (!$old->img_name) continue;

            // img_choice=1がメイン画像
            $flag = $old->img_choice == 1 ? 1 : 0;

            DB::table('item_images')->insert([
                'item_id'    => $itemId,
                'filename'   => 'item/' . $old->img_name,
                'flag'       => $flag,
                'sort'       => is_numeric($old->img_sort) ? (int) $old->img_sort : 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->info('item_images 移行完了');
    }
}
