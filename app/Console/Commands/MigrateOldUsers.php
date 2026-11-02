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


class MigrateOldUsers extends Command
{
    protected $signature = 'migrate:old-users';
    protected $description = '旧DBから新DBへユーザー情報を移行する';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $oldUsers = DB::connection('old_mysql')->table('users')->get();

        foreach ($oldUsers as $old) {
            // メールアドレスが重複しないようチェック
            if (DB::table('members')->where('email', Crypt::encryptString($old->user_mail))->exists()) {
                $this->warn("Skipped-Email: {$old->user_mail} already exists.");
                continue;
            }
            // loginIDが重複しないようチェック
            if (DB::table('members')->where('loginid', Str::before($old->user_mail, '@'))->exists()) {
                $this->warn("Skipped-LoginId: {$old->user_mail} ");
                continue;
            }

            DB::table('members')->insert([
                'id' => $old->user_id,
                'point' => $old->user_point,
                'name' => Crypt::encryptString($old->user_name),
                'email' => Crypt::encryptString($old->user_mail),
                'loginid' => Str::before($old->user_mail, '@'),
                'password' => Hash::make($old->password), // ハッシュ
                'birth' => empty($old->user_birth) || $old->user_birth === '0000-00-00' ? null : $old->user_birth,
                'gender' => 0,
                'kind' => 1,
                'check' => $old->user_auth,
                'tel' => Crypt::encryptString($old->user_tel),
                'line' => null,
                'sns' => null,
                'url' => $old->user_url,
                'memo' => null,
                'memo_share' => null,
                'nickname' => $old->user_nickname,
                'member_no' => null,
                'favo' => null,
                'filename' => null,
                'created_at' => Carbon::parse($old->user_regist),
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
