<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MigrateOldShops extends Command
{
    protected $signature = 'migrate:old-shops';
    protected $description = '旧DBから新DBへショップ・グループ情報を移行する (shop_data→shops, shop_group→shop_groups)';

    public function handle()
    {
        // shop_data → shops
        $oldShops = DB::connection('old_mysql')
            ->table('shop_data')
            ->orderBy('shop_id')
            ->get();

        foreach ($oldShops as $old) {
            $data = [
                'shop_name'        => $old->shop_name,
                'shop_active'      => $old->shop_active,
                'shop_idname'      => $old->shop_idname ?? null,
                'shop_sub_name'    => $old->shop_name_sub ?? null,
                'shop_copy'        => $old->shop_subtitle ?? null,
                'shop_area'        => $old->shop_area ?? null,
                'shop_tel'         => $old->shop_tel,
                'shop_tel2'        => $old->shop_tel2 ?? null,
                'shop_open'        => $old->shop_open,
                'shop_address'     => $old->shop_address,
                'shop_mail'        => $old->shop_mail,
                'shop_domain'      => $old->shop_domain ?? null,
                'shop_url'         => $old->shop_url,
                'shop_analytics'   => $old->shop_analytics ?? null,
                'shop_x'           => $old->shop_twitter,
                'shop_line'        => $old->shop_line ?? null,
                'shop_line_id'     => $old->shop_line_url ?? null,
                'shop_youtube'     => $old->shop_youtube ?? null,
                'shop_tiktok'      => $old->shop_tiktok ?? null,
                'shop_chat'        => $old->shop_chat ?? null,
                'shop_reservation' => $old->shop_reserve ?? null,
                'shop_recruit'     => $old->shop_recruit ?? null,
                'shop_recruit_mens'=> $old->shop_recruit_mens ?? null,
                'shop_payment'     => $old->shop_payment ?? null,
                'shop_shame'       => $old->shop_shame ?? null,
                'shop_sokuhime'    => $old->shop_sokuhime ?? null,
                'shop_movie'       => $old->shop_movie ?? null,
                'shop_review'      => $old->shop_review ?? null,
                'updated_at'       => now(),
            ];

            if (DB::table('shops')->where('id', $old->shop_id)->exists()) {
                DB::table('shops')->where('id', $old->shop_id)->update($data);
                $this->line("Updated shop: {$old->shop_name} (id={$old->shop_id})");
            } else {
                DB::table('shops')->insert(array_merge(['id' => $old->shop_id, 'created_at' => now()], $data));
                $this->line("Inserted shop: {$old->shop_name} (id={$old->shop_id})");
            }
        }

        // shop_group → shop_groups
        $oldGroups = DB::connection('old_mysql')
            ->table('shop_group')
            ->orderBy('group_id')
            ->get();

        foreach ($oldGroups as $old) {
            $data = [
                'is_publish' => $old->group_active,
                'name'       => $old->group_name,
                'url'        => $old->group_url ?: null,
                'sort'       => $old->group_sort,
                'filename'   => $old->group_filename ?: null,
                'updated_at' => now(),
            ];

            if (DB::table('shop_groups')->where('id', $old->group_id)->exists()) {
                DB::table('shop_groups')->where('id', $old->group_id)->update($data);
                $this->line("Updated shop_group: {$old->group_name}");
            } else {
                DB::table('shop_groups')->insert(array_merge(['id' => $old->group_id, 'created_at' => now()], $data));
                $this->line("Inserted shop_group: {$old->group_name}");
            }
        }

        $this->info('shops / shop_groups 移行完了');
    }
}
