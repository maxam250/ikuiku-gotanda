<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class DisableExternal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'external:disable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '外部サーバーのデータベース接続を無効化します';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('外部サーバー接続を無効化しています...');
        
        try {
            // .envファイルのパス
            $envPath = base_path('.env');
            
            if (!File::exists($envPath)) {
                $this->error('.envファイルが見つかりません');
                return 1;
            }
            
            // .envファイルの内容を読み込み
            $envContent = File::get($envPath);
            
            // EXTERNAL_DB_ENABLEDをfalseに設定
            $envContent = $this->updateEnvValue($envContent, 'EXTERNAL_DB_ENABLED', 'false');
            
            // .envファイルに書き込み
            File::put($envPath, $envContent);
            
            $this->info('✓ 外部サーバー接続が無効化されました');
            $this->info('設定を反映するために、config:clearコマンドを実行してください');
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('✗ 外部サーバー接続の無効化に失敗しました');
            $this->error('エラー: ' . $e->getMessage());
            return 1;
        }
    }
    
    /**
     * .envファイルの値を更新
     */
    private function updateEnvValue($content, $key, $value)
    {
        $pattern = "/^{$key}=.*/m";
        $replacement = "{$key}={$value}";
        
        if (preg_match($pattern, $content)) {
            return preg_replace($pattern, $replacement, $content);
        } else {
            return $content . "\n{$key}={$value}";
        }
    }
}
