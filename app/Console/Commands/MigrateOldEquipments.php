<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateOldEquipments extends Command
{
    protected $signature = 'migrate:old-equipments';
    protected $description = '旧DBから新DBへホテル/設備情報を移行する (hotel_cate→equipment_cates, hotel_data→equipments)';

    public function handle()
    {
        // hotel_cate → equipment_cates
        $oldCates = DB::connection('old_mysql')
            ->table('hotel_cate')
            ->orderBy('hotel_cate_id')
            ->get();

        foreach ($oldCates as $old) {
            $data = [
                'active'     => $old->hotel_cate_active,
                'title'      => $old->hotel_cate_title,
                'text'       => null,
                'flag'       => 0,
                'link'       => $old->hotel_cate_map ?: null,
                'sort'       => 0,
                'updated_at' => now(),
            ];

            if (DB::table('equipment_cates')->where('id', $old->hotel_cate_id)->exists()) {
                DB::table('equipment_cates')->where('id', $old->hotel_cate_id)->update($data);
            } else {
                DB::table('equipment_cates')->insert(array_merge(
                    ['id' => $old->hotel_cate_id, 'created_at' => now()],
                    $data
                ));
            }
        }

        $this->info('equipment_cates 移行完了');

        // hotel_data → equipments
        $oldEquipments = DB::connection('old_mysql')
            ->table('hotel_data')
            ->orderBy('hotel_id')
            ->get();

        foreach ($oldEquipments as $old) {
            $data = [
                'active'    => 1,
                'cate_id'   => (string) $old->hotel_cate_id,
                'title'     => $old->hotel_name,
                'text1'     => $old->hotel_access ?: null,
                'text2'     => $old->hotel_tel ?: null,
                'text3'     => $old->hotel_address ?: null,
                'text4'     => $old->hotel_price ?: null,
                'text5'     => null,
                'flag1'     => 0,
                'flag2'     => 0,
                'flag3'     => 0,
                'flag4'     => 0,
                'flag5'     => 0,
                'link'      => $old->hotel_url ?: null,
                'filename'  => null,
                'sort'      => 0,
                'updated_at'=> now(),
            ];

            if (DB::table('equipments')->where('id', $old->hotel_id)->exists()) {
                DB::table('equipments')->where('id', $old->hotel_id)->update($data);
            } else {
                DB::table('equipments')->insert(array_merge(
                    ['id' => $old->hotel_id, 'created_at' => now()],
                    $data
                ));
            }
        }

        $this->info('equipments 移行完了');
    }
}
