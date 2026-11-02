<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ExternalDatabaseService;
use Illuminate\Support\Facades\DB;

class TestExternalConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'external:test-connection';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '外部サーバーのデータベース接続をテストします';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('外部サーバー接続をテストしています...');
        
        // 現在の設定を表示
        $this->info('現在の設定:');
        $this->info('- ホスト: ' . env('EXTERNAL_DB_HOST', '127.0.0.1'));
        $this->info('- ポート: ' . env('EXTERNAL_DB_PORT', '3306'));
        $this->info('- データベース: ' . env('EXTERNAL_DB_DATABASE', 'ppro-admins_admin_new'));
        $this->info('- ユーザー: ' . env('EXTERNAL_DB_USERNAME', 'root'));
        $this->info('- パスワード: ' . (env('EXTERNAL_DB_PASSWORD') ? '設定済み' : '未設定'));
        $this->info('');

        try {
            // 外部サーバー接続をテスト
            $connection = DB::connection('external_mysql');
            $pdo = $connection->getPdo();
            
            $this->info('✓ 外部サーバー接続に成功しました');
            
            // データベース名を取得
            $database = $connection->getDatabaseName();
            $this->info("接続先データベース: {$database}");
            
            // テーブル一覧を取得
            $tables = $connection->select('SHOW TABLES');
            $tableNames = array_map(function($table) {
                return array_values((array)$table)[0];
            }, $tables);
            
            $this->info('利用可能なテーブル:');
            foreach ($tableNames as $tableName) {
                $this->line("  - {$tableName}");
            }
            
            // reservationsテーブルの件数を確認
            if (in_array('reservations', $tableNames)) {
                $count = $connection->table('reservations')->count();
                $this->info("reservationsテーブルの件数: {$count}件");
                
                // テーブル構造も確認
                $columns = $connection->select('DESCRIBE reservations');
                $this->info('reservationsテーブルの構造:');
                foreach ($columns as $column) {
                    $this->line("  - {$column->Field} ({$column->Type})");
                }
            } else {
                $this->warn('reservationsテーブルが見つかりません');
            }
            
        } catch (\Exception $e) {
            $this->error('✗ 外部サーバー接続に失敗しました');
            $this->error('エラー: ' . $e->getMessage());
            
            $this->info('');
            $this->info('設定を確認してください:');
            $this->info('- .envファイルのEXTERNAL_DB_*設定');
            $this->info('- MySQLサーバーが起動しているか');
            $this->info('- データベース「ppro-admins_admin_new」が存在するか');
            $this->info('- ユーザー「root」にアクセス権限があるか');
            
            return 1;
        }

        return 0;
    }
} 