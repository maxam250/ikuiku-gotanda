<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateOldOptions extends Command
{
    protected $signature = 'migrate:old-options';
    protected $description = '旧DBから新DBへオプション情報を移行する (option_free→options, option_pay→option_pays)';

    public function handle()
    {
        // option_free → options
        $oldOptions = DB::connection('old_mysql')
            ->table('option_free')
            ->orderBy('option_free_id')
            ->get();

        foreach ($oldOptions as $old) {
            $data = [
                'cate'       => 1,
                'title'      => $old->option_free_name ?? '',
                'body'       => $old->option_free_text ?: null,
                'filename'   => $old->option_free_img ?: null,
                'sort'       => is_numeric($old->option_free_sort) ? (int) $old->option_free_sort : 0,
                'updated_at' => now(),
            ];

            if (DB::table('options')->where('id', $old->option_free_id)->exists()) {
                DB::table('options')->where('id', $old->option_free_id)->update($data);
            } else {
                DB::table('options')->insert(array_merge(
                    ['id' => $old->option_free_id, 'created_at' => now()],
                    $data
                ));
            }
        }

        $this->info('options 移行完了');

        // option_pay → option_pays
        $oldOptionPays = DB::connection('old_mysql')
            ->table('option_pay')
            ->orderBy('option_pay_id')
            ->get();

        foreach ($oldOptionPays as $old) {
            $data = [
                'cate'       => 0,
                'title'      => $old->option_pay_name ?? '',
                'body'       => null,
                'filename'   => $old->option_pay_img ?: null,
                'price'      => (string) ($old->option_pay_price ?? 0),
                'sort'       => is_numeric($old->option_pay_sort) ? (int) $old->option_pay_sort : 0,
                'updated_at' => now(),
            ];

            if (DB::table('option_pays')->where('id', $old->option_pay_id)->exists()) {
                DB::table('option_pays')->where('id', $old->option_pay_id)->update($data);
            } else {
                DB::table('option_pays')->insert(array_merge(
                    ['id' => $old->option_pay_id, 'created_at' => now()],
                    $data
                ));
            }
        }

        $this->info('option_pays 移行完了');
    }
}
