<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;


use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;


//$encrypted = Crypt::encryptString('平文の値');
//$decrypted = Crypt::decryptString($encrypted);


class MigrateOldImgGalleries extends Command
{
    protected $signature = 'migrate:old-img-galleries';
    protected $description = '旧DBから新DBへギャラリー画像情報を移行する';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $oldImgGalleries = DB::connection('old_mysql')->table('gallery_img_data')->get();

        $oldImgGalleries = DB::connection('old_mysql')
                ->table('gallery_img_data')
                ->orderBy('gallery_img_id')
                ->get();

        foreach ($oldImgGalleries as $old) {


            DB::table('gallery_images')->insert([
                'gallery_id' => $old->gallery_img_i_id,
                'filename' => $old->gallery_img_name,
                'caption' => null,
                'sort' => $old->gallery_img_sort,
            ]);

        }

        $this->info('移行完了');
    }

}
