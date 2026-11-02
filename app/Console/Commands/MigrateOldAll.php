<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

/**
 * 旧DBから新DBへ全データを正しい順序で移行する
 *
 * 実行順序（依存関係に従う）:
 *  1. shops / shop_groups      ← 依存なし
 *  2. events                   ← 依存なし
 *  3. links                    ← 依存なし
 *  4. costumes                 ← 依存なし
 *  5. equipment_cates / equipments
 *  6. icon_items               ← 依存なし
 *  7. options / option_pays    ← 依存なし
 *  8. profiles                 ← 依存なし
 *  9. rank_cates / ranks       ← rank_cates が先
 * 10. items                    ← shops が先
 * 11. item_images              ← items が先
 * 12. item_icon_item / item_option / item_option_pay / item_profile / item_rank
 * 13. pickups                  ← items が先
 * 14. newfaces                 ← items が先
 * 15. schedules (既存コマンド) ← items が先
 *
 * ※ migrate:old-schedules は item_id マッピングが内部にハードコードされています。
 *   migrate:old-items 実行後は元の item_id がそのまま保持されるため、
 *   MigrateOldSchedules.php 内の $mapping を空配列 [] にすることを推奨します。
 */
class MigrateOldAll extends Command
{
    protected $signature = 'migrate:old-all
                            {--skip-schedules : スケジュール移行をスキップする}';
    protected $description = '旧DBから新DBへ全データを一括移行する';

    public function handle()
    {
        $steps = [
            // ['migrate:old-shops',           'shops / shop_groups'],
            // ['migrate:old-events',          'events'],
            // ['migrate:old-links',           'links'],
            // ['migrate:old-costumes',        'costumes'],
            // ['migrate:old-equipments',      'equipment_cates / equipments'],
            ['migrate:old-icons',           'icon_items'],
            // ['migrate:old-options',         'options / option_pays'],
            ['migrate:old-profiles',        'profiles'],
            ['migrate:old-ranks',           'rank_cates / ranks'],
            ['migrate:old-items',           'items'],
            ['migrate:old-item-images',     'item_images'],
            ['migrate:old-item-relations',  'item_icon_item / item_option / item_option_pay / item_profile / item_rank'],
            ['migrate:old-pickups',         'pickups'],
            ['migrate:old-newfaces',        'newfaces'],
            ['migrate:old-news',            'news'],
        ];

        foreach ($steps as [$command, $label]) {
            $this->info("▶ {$label} ...");
            try {
                Artisan::call($command, [], $this->output);
            } catch (\Throwable $e) {
                $this->error("  エラー: {$e->getMessage()}");
                if (!$this->confirm('エラーが発生しました。続行しますか？')) {
                    return 1;
                }
            }
        }

        if (!$this->option('skip-schedules')) {
            $this->info('▶ schedules ...');
            $this->warn('  ※ migrate:old-schedules は item_id マッピングが内部に設定されています。');
            $this->warn('  ※ migrate:old-items 実行済みの場合、MigrateOldSchedules.php の $mapping を [] に修正してください。');
            if ($this->confirm('migrate:old-schedules を実行しますか？')) {
                Artisan::call('migrate:old-schedules', [], $this->output);
            }
        }

        $this->info('✓ 全データ移行完了');
        return 0;
    }
}
