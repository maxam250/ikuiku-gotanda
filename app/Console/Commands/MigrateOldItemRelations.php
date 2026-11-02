<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateOldItemRelations extends Command
{
    protected $signature = 'migrate:old-item-relations';
    protected $description = '旧DBから新DBへキャスト関連情報を移行する (icon/option/profile/rank 中間テーブル)';

    public function handle()
    {
        $this->migrateIconItems();
        $this->migrateItemOptions();
        $this->migrateItemOptionPays();
        $this->migrateItemProfiles();
        $this->migrateItemRanks();
    }

    // item_icon_data → item_icon_item
    private function migrateIconItems(): void
    {
        $rows = DB::connection('old_mysql')
            ->table('item_icon_data')
            ->whereNotNull('item_mid_item_id')
            ->whereNotNull('item_mid_cate_mid_id')
            ->orderBy('item_mid_data_id')
            ->get();

        $processedItems = [];

        foreach ($rows as $old) {
            $itemId = $old->item_mid_item_id;

            if (!in_array($itemId, $processedItems)) {
                DB::table('item_icon_item')->where('item_id', $itemId)->delete();
                $processedItems[] = $itemId;
            }

            // icon_items に存在するか確認
            if (!DB::table('icon_items')->where('id', $old->item_mid_cate_mid_id)->exists()) continue;
            // items に存在するか確認
            if (!DB::table('items')->where('id', $itemId)->exists()) continue;

            DB::table('item_icon_item')->insert([
                'item_id'      => $itemId,
                'item_icon_id' => $old->item_mid_cate_mid_id,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        $this->info('item_icon_item 移行完了');
    }

    // option_free_data → item_option
    private function migrateItemOptions(): void
    {
        $rows = DB::connection('old_mysql')
            ->table('option_free_data')
            ->whereNotNull('option_free_item_id')
            ->whereNotNull('option_free_option_free_id')
            ->orderBy('option_free_data_id')
            ->get();

        $processedItems = [];

        foreach ($rows as $old) {
            $itemId = $old->option_free_item_id;

            if (!in_array($itemId, $processedItems)) {
                DB::table('item_option')->where('item_id', $itemId)->delete();
                $processedItems[] = $itemId;
            }

            if (!DB::table('options')->where('id', $old->option_free_option_free_id)->exists()) continue;
            if (!DB::table('items')->where('id', $itemId)->exists()) continue;

            DB::table('item_option')->insert([
                'item_id'    => $itemId,
                'option_id'  => $old->option_free_option_free_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->info('item_option 移行完了');
    }

    // option_pay_data → item_option_pay
    private function migrateItemOptionPays(): void
    {
        $rows = DB::connection('old_mysql')
            ->table('option_pay_data')
            ->whereNotNull('option_pay_item_id')
            ->whereNotNull('option_pay_option_pay_id')
            ->orderBy('option_pay_data_id')
            ->get();

        $processedItems = [];

        foreach ($rows as $old) {
            $itemId = $old->option_pay_item_id;

            if (!in_array($itemId, $processedItems)) {
                DB::table('item_option_pay')->where('item_id', $itemId)->delete();
                $processedItems[] = $itemId;
            }

            if (!DB::table('option_pays')->where('id', $old->option_pay_option_pay_id)->exists()) continue;
            if (!DB::table('items')->where('id', $itemId)->exists()) continue;

            DB::table('item_option_pay')->insert([
                'item_id'       => $itemId,
                'option_pay_id' => $old->option_pay_option_pay_id,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        $this->info('item_option_pay 移行完了');
    }

    // prof_item_data → item_profile
    private function migrateItemProfiles(): void
    {
        $rows = DB::connection('old_mysql')
            ->table('prof_item_data')
            ->whereNotNull('item_id')
            ->whereNotNull('p_id')
            ->orderBy('pi_id')
            ->get();

        $processedItems = [];

        foreach ($rows as $old) {
            $itemId = $old->item_id;

            if (!in_array($itemId, $processedItems)) {
                DB::table('item_profile')->where('item_id', $itemId)->delete();
                $processedItems[] = $itemId;
            }

            if (!DB::table('profiles')->where('id', $old->p_id)->exists()) continue;
            if (!DB::table('items')->where('id', $itemId)->exists()) continue;

            DB::table('item_profile')->insert([
                'item_id'    => $itemId,
                'profile_id' => $old->p_id,
                'content'    => $old->p_text ?: null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->info('item_profile 移行完了');
    }

    // item_rank_data → item_rank
    // c_rank_data_id = cms_ranking_data.rank_id → rank_id
    // c_rank_c_id    = item_data.item_id        → item_id
    private function migrateItemRanks(): void
    {
        $rows = DB::connection('old_mysql')
            ->table('item_rank_data')
            ->whereNotNull('c_rank_data_id')
            ->whereNotNull('c_rank_c_id')
            ->orderBy('c_rank_id')
            ->get();

        $processedRanks = [];

        foreach ($rows as $old) {
            $rankId = $old->c_rank_data_id;

            if (!in_array($rankId, $processedRanks)) {
                DB::table('item_rank')->where('rank_id', $rankId)->delete();
                $processedRanks[] = $rankId;
            }

            if (!DB::table('ranks')->where('id', $rankId)->exists()) continue;
            if (!DB::table('items')->where('id', $old->c_rank_c_id)->exists()) continue;

            DB::table('item_rank')->insert([
                'item_id'    => $old->c_rank_c_id,
                'rank_id'    => $rankId,
                'no'         => $old->c_rank_num ?? 0,
                'text'       => $old->c_rank_text ?: null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->info('item_rank 移行完了');
    }
}
