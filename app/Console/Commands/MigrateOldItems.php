<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MigrateOldItems extends Command
{
    protected $signature = 'migrate:old-items';
    protected $description = '旧DBから新DBへキャスト情報を移行する (item_data→items)';

    public function handle()
    {
        $oldItems = DB::connection('old_mysql')
            ->table('item_data')
            ->orderBy('item_id')
            ->get();

        $count = 0;

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($oldItems as $old) {
            // item_active: 1=表示 → 1, 2=非表示 → 0
            $active = ($old->item_active == 1) ? 1 : 0;

            $data = [
                'shop_id'              => $old->item_main_shop ?? $old->item_shop ?? 0,
                'item_name'            => $old->item_name ?? '',
                'item_active'          => $active,
                'item_cate'            => $old->item_cate ?? 0,
                'item_new'             => $old->item_new ?? 0,
                'item_age'             => $old->item_age > 0 ? (int) $old->item_age : null,
                'item_height'          => $this->toInt($old->item_height),
                'item_b'               => $this->toInt($old->item_b),
                'item_w'               => $this->toInt($old->item_w),
                'item_h'               => $this->toInt($old->item_h),
                'item_cup'             => $old->item_cup ?: null,
                'item_catch'           => $old->item_catch ?: null,
                'item_video'           => ($old->item_movie_url ?? null) ?: (!empty($old->item_movie) ? "item/video/{$old->item_movie}" : null),
                'premium_rank_id'      => (int) ($old->item_premier ?? 0) + 1,
                'item_memo'            => ($old->item_text ?? null) ?: null,
                'item_comment'         => $old->item_comment ?: null,
                'item_comment2'        => $old->item_comment2 ?: null,
                'item_comment_nominate'=> ($old->item_comment_shimei ?? null) ?: null,
                'item_comment_faq'     => $old->item_comment_faq ?: null,
                'item_twitter'         => $old->item_twitter ?: null,
                'item_shame_wiget'     => ($old->item_shame_wiget ?? null) ?: null,
                'item_review_wiget'    => ($old->item_review_wiget ?? null) ?: null,
                'item_sch_wiget'       => ($old->item_sch_wiget ?? null) ?: null,
                'default_sch_start'    => $this->normalizeTime($old->item_default_sch),
                'default_sch_end'      => $this->normalizeTime($old->item_default_sch2),
                'email'                => ($old->item_mail ?? null) ?: null,
                'item_url'             => ($old->item_url_shinjuku ?? null) ?: ($old->item_url ?: null),
                'item_sort'            => $old->item_sort_shinjuku ?? $old->item_sort ?? 0,
                'loginid'              => $old->item_english ?: null,
                'password'             => !empty($old->item_english) ? Hash::make(!empty($old->item_pass_text) ? $old->item_pass_text : '0000') : null,
                'item_pass_text'       => !empty($old->item_english) ? (!empty($old->item_pass_text) ? $old->item_pass_text : '0000') : null,
                'updated_at'           => $old->item_lastupdate ? Carbon::parse($old->item_lastupdate) : now(),
            ];

            if (DB::table('items')->where('id', $old->item_id)->exists()) {
                DB::table('items')->where('id', $old->item_id)->update($data);
            } else {
                DB::table('items')->insert(array_merge(
                    [
                        'id'         => $old->item_id,
                        'created_at' => $old->item_regist ? Carbon::parse($old->item_regist) : now(),
                    ],
                    $data
                ));
            }

            $count++;
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info("items 移行完了 ({$count}件)");
        $this->warn('※ MigrateOldSchedules の item_id マッピングは item_data の元IDを使用するよう要確認');
    }

    private function normalizeTime(?string $time): ?string
    {
        if (!$time) return null;
        $hour = (int) explode(':', $time)[0];
        return $hour >= 25 ? null : $time;
    }

    private function toInt($value): ?int
    {
        if ($value === null || $value === '') return null;
        $int = (int) $value;
        return $int > 0 ? $int : null;
    }
}
