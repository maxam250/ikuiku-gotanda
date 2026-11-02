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


class MigrateOldGalleries extends Command
{
    protected $signature = 'migrate:old-galleries';
    protected $description = '旧DBから新DBへギャラリー情報を移行する';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $oldGalleries = DB::connection('old_mysql')->table('gallery_data')->get();
        $oldGalleries = DB::connection('old_mysql')
                ->table('gallery_data')
                ->orderBy('gallery_id')
                ->get();
        foreach ($oldGalleries as $old) {


            DB::table('galleries')->insert([
                'id' => $old->gallery_id,
                'active' => $old->gallery_active,
                'item_id' => $old->gallery_item_id,
                'member_id' => $old->gallery_user_id,
                'title' => $old->gallery_name,
                'description' => $old->gallery_point,
                'sort' => $old->gallery_sort,
                'flag' => 0,
                'created_at' => Carbon::parse($old->gallery_regist),
                'updated_at' => now(),
            ]);


            // パスワード再設定用トークンを発行（任意）
            // $token = Str::random(64);
            // DB::table('password_resets')->insert([
            //     'email' => $old->mail_address,
            //     'token' => Hash::make($token),
            //     'created_at' => now(),
            // ]);

            // 通知メール送信（Mailクラスを別途用意）
            // Mail::to($old->mail_address)->send(new PasswordResetNotice($token));
        }

        $this->info('移行完了');
    }

}
