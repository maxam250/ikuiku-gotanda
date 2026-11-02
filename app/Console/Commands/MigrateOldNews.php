<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class MigrateOldNews extends Command
{
    protected $signature = 'migrate:old-news';
    protected $description = '旧DBから新DBへ新着情報を移行する (news_data→news)';

    public function handle()
    {
        if (!Schema::connection('old_mysql')->hasTable('news_data')) {
            $this->warn('news_data が存在しないためスキップ');
            return;
        }

        $oldNews = DB::connection('old_mysql')
            ->table('news_data')
            ->orderBy('news_id')
            ->get();

        foreach ($oldNews as $old) {
            // news_frag: 1=表示 / 0=非表示
            $date = $old->news_date ? Carbon::parse($old->news_date) : now();

            $data = [
                'item_id'    => !empty($old->news_item_id) ? $old->news_item_id : null,
                'active'     => (int) $old->news_frag === 1 ? 1 : 0,
                'title'      => $old->news_title ?: '新着情報',
                'cate'       => 0,
                'body'       => $old->news_text ?: null,
                'link'       => $old->news_url ?: null,
                'filename'   => $old->news_image ?: null,
                'timer_flag' => 0,
                // 旧サイトは news_id の降順表示。新サイトは sort 昇順→id 降順なので sort は 0 で揃える
                'sort'       => 0,
                'updated_at' => $date,
            ];

            if (DB::table('news')->where('id', $old->news_id)->exists()) {
                DB::table('news')->where('id', $old->news_id)->update($data);
            } else {
                DB::table('news')->insert(array_merge(
                    ['id' => $old->news_id, 'created_at' => $date],
                    $data
                ));
            }
        }

        $this->info('news 移行完了 (' . $oldNews->count() . '件)');
    }
}
