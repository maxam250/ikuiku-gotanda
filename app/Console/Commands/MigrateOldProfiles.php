<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateOldProfiles extends Command
{
    protected $signature = 'migrate:old-profiles';
    protected $description = '旧DBから新DBへプロフィール項目を移行する (prof_data→profiles)';

    public function handle()
    {
        $oldProfiles = DB::connection('old_mysql')
            ->table('prof_data')
            ->orderBy('p_id')
            ->get();

        foreach ($oldProfiles as $old) {
            $data = [
                'title'      => $old->p_name ?? '',
                'sort'       => is_numeric($old->p_sort) ? (int) $old->p_sort : 0,
                'updated_at' => now(),
            ];

            if (DB::table('profiles')->where('id', $old->p_id)->exists()) {
                DB::table('profiles')->where('id', $old->p_id)->update($data);
            } else {
                DB::table('profiles')->insert(array_merge(
                    ['id' => $old->p_id, 'created_at' => now()],
                    $data
                ));
            }
        }

        $this->info('profiles 移行完了');
    }
}
