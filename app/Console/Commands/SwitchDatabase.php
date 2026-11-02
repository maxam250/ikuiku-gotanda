<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ExternalDatabaseService;

class SwitchDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'external:switch {mode : local または external}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'データベース接続をローカルと外部サーバーで切り替えます';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mode = $this->argument('mode');
        
        if (!in_array($mode, ['local', 'external'])) {
            $this->error('無効なモードです。local または external を指定してください。');
            return 1;
        }

        $this->info("データベース接続を {$mode} に切り替えています...");

        // .envファイルの内容を読み込み
        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            $this->error('.envファイルが見つかりません。');
            return 1;
        }

        $envContent = file_get_contents($envPath);
        
        // USE_EXTERNAL_DATABASEの値を更新
        $newValue = $mode === 'external' ? 'true' : 'false';
        
        if (preg_match('/^USE_EXTERNAL_DATABASE=.*$/m', $envContent)) {
            // 既存の設定を更新
            $envContent = preg_replace(
                '/^USE_EXTERNAL_DATABASE=.*$/m',
                "USE_EXTERNAL_DATABASE={$newValue}",
                $envContent
            );
        } else {
            // 新しい設定を追加
            $envContent .= "\n# 外部サーバー接続設定\nUSE_EXTERNAL_DATABASE={$newValue}\n";
        }

        // .envファイルに書き込み
        if (file_put_contents($envPath, $envContent)) {
            $this->info("✓ データベース接続を {$mode} に切り替えました");
            
            if ($mode === 'external') {
                $this->info('');
                $this->info('外部サーバー接続を有効にしました。');
                $this->info('接続をテストするには以下を実行してください:');
                $this->info('php artisan external:test-connection');
            } else {
                $this->info('');
                $this->info('ローカルサーバー接続を有効にしました。');
            }
            
            // 設定キャッシュをクリア
            $this->call('config:clear');
            $this->info('設定キャッシュをクリアしました');
            
        } else {
            $this->error('.envファイルの更新に失敗しました。');
            return 1;
        }

        return 0;
    }
} 