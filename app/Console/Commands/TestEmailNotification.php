<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\MemberMessageToAdminNotification;
use App\Mail\ItemMessageToAdminNotification;
use App\Models\Message;
use App\Models\MessageItem;
use App\Models\Admin;

class TestEmailNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email-notification {type=all : Type of notification to test (member|item|all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email notification functionality for messages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');

        $this->info('メール通知テストを開始します...');

        // 管理者が存在するかチェック
        $admins = Admin::all();
        if ($admins->isEmpty()) {
            $this->error('管理者が見つかりません。まず管理者アカウントを作成してください。');
            return 1;
        }

        $this->info("管理者数: {$admins->count()}");

        if ($type === 'member' || $type === 'all') {
            $this->testMemberNotification();
        }

        if ($type === 'item' || $type === 'all') {
            $this->testItemNotification();
        }

        $this->info('テストが完了しました。');
        return 0;
    }

    private function testMemberNotification()
    {
        $this->info('会員メッセージ通知をテスト中...');

        // ダミーメッセージを作成
        $message = new Message([
            'sender_id' => 1,
            'sender_type' => 'member',
            'receiver_id' => 1,
            'receiver_type' => 'admin',
            'title' => 'テストメッセージ',
            'message' => 'これは会員からのテストメッセージです。',
            'created_at' => now(),
        ]);

        try {
            $admins = Admin::all();
            foreach ($admins as $admin) {
                $this->info("管理者 {$admin->email} にメール送信中...");
                Mail::to($admin->email)
                    ->bcc(config('mail.shop_mail2'))
                    ->send(new MemberMessageToAdminNotification($message));
            }
            $this->info('✓ 会員メッセージ通知の送信が成功しました。');
        } catch (\Exception $e) {
            $this->error('✗ 会員メッセージ通知の送信に失敗しました: ' . $e->getMessage());
        }
    }

    private function testItemNotification()
    {
        $this->info('キャストメッセージ通知をテスト中...');

        // ダミーメッセージを作成
        $message = new MessageItem([
            'sender_id' => 1,
            'sender_type' => 'item',
            'receiver_id' => 1,
            'receiver_type' => 'admin',
            'title' => 'テストメッセージ',
            'message' => 'これはキャストからのテストメッセージです。',
            'created_at' => now(),
        ]);

        try {
            $admins = Admin::all();
            foreach ($admins as $admin) {
                $this->info("管理者 {$admin->email} にメール送信中...");
                Mail::to($admin->email)
                    ->bcc(config('mail.shop_mail2'))
                    ->send(new ItemMessageToAdminNotification($message));
            }
            $this->info('✓ キャストメッセージ通知の送信が成功しました。');
        } catch (\Exception $e) {
            $this->error('✗ キャストメッセージ通知の送信に失敗しました: ' . $e->getMessage());
        }
    }
}




