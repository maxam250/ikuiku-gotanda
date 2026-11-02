<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class MigrateOldRanks extends Command
{
    protected $signature = 'migrate:old-ranks';
    protected $description = '旧DBから新DBへランキング情報を移行する (cms_rank_cate→rank_cates, cms_ranking_data→ranks)';

    public function handle()
    {
        // cms_rank_cate → rank_cates
        // ※旧DBのテーブル名は cms_rank_cate（cms_ranking_cateでない点に注意）
        // ※店舗によっては cms_rank_cate が存在しないためスキップ
        $oldCates = Schema::connection('old_mysql')->hasTable('cms_rank_cate')
            ? DB::connection('old_mysql')
                ->table('cms_rank_cate')
                ->orderBy('rank_cate_id')
                ->get()
            : collect();

        foreach ($oldCates as $old) {
            $data = [
                'active'     => $old->rank_cate_active ?? 1,
                'title'      => $old->rank_cate_title,
                'body'       => $old->rank_cate_text ?: null,
                'sort'       => 0,
                'updated_at' => now(),
            ];

            if (DB::table('rank_cates')->where('id', $old->rank_cate_id)->exists()) {
                DB::table('rank_cates')->where('id', $old->rank_cate_id)->update($data);
            } else {
                DB::table('rank_cates')->insert(array_merge(
                    ['id' => $old->rank_cate_id, 'created_at' => Carbon::parse($old->rank_cate_regist)],
                    $data
                ));
            }
        }

        $this->info('rank_cates 移行完了');

        // cms_ranking_data → ranks
        $oldRanks = DB::connection('old_mysql')
            ->table('cms_ranking_data')
            ->orderBy('rank_id')
            ->get();

        foreach ($oldRanks as $old) {
            // rank_hide: 0=非表示, 1=TOP, 2=rankページのみ, 3=全て → 0=非表示, 1=表示
            $active = ($old->rank_hide == 0) ? 0 : 1;

            $data = [
                'active'     => $active,
                'cate_id'    => ($old->rank_cate ?? null) ?: null,
                'title'      => $old->rank_title ?? '',
                'body'       => null,
                'filename'   => ($old->rank_img ?? null) ?: null,
                'sort'       => $old->rank_sort ?? 0,
                'position'   => $old->rank_hide ?? 0,
                'term_start' => $old->rank_st ?: null,
                'term_end'   => $old->rank_en ?: null,
                'updated_at' => now(),
            ];

            if (DB::table('ranks')->where('id', $old->rank_id)->exists()) {
                DB::table('ranks')->where('id', $old->rank_id)->update($data);
            } else {
                DB::table('ranks')->insert(array_merge(
                    ['id' => $old->rank_id, 'created_at' => Carbon::parse($old->rank_regist)],
                    $data
                ));
            }
        }

        $this->info('ranks 移行完了');
    }
}
