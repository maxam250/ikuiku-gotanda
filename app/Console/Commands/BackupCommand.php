<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BackupService;

class BackupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:run {--type=all : バックアップタイプ (images, database, all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '画像ファイルとデータベースのバックアップを実行';

    protected $backupService;

    public function __construct(BackupService $backupService)
    {
        parent::__construct();
        $this->backupService = $backupService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->option('type');

        $this->info('バックアップ処理を開始します...');

        if ($type === 'all' || $type === 'images') {
            $this->info('画像ファイルのバックアップを実行中...');
            $filename = $this->backupService->backupImages();
            
            if ($filename) {
                $this->info("✓ 画像バックアップ完了: {$filename}");
            } else {
                $this->error("✗ 画像バックアップに失敗しました");
            }
        }

        if ($type === 'all' || $type === 'database') {
            $this->info('データベースのバックアップを実行中...');
            $filename = $this->backupService->backupDatabase();
            
            if ($filename) {
                $this->info("✓ データベースバックアップ完了: {$filename}");
            } else {
                $this->error("✗ データベースバックアップに失敗しました");
            }
        }

        $this->info('バックアップ処理が完了しました。');
        
        return Command::SUCCESS;
    }
}






