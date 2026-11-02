<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PharData;
use Phar;
use Exception;

class BackupService
{
    protected $backupPath;
    protected $imagesPath;
    protected $retentionDays;

    public function __construct()
    {
        $this->backupPath = storage_path('app/backups');
        $this->imagesPath = storage_path('app/public');
        $this->retentionDays = env('BACKUP_RETENTION_DAYS', 7);
    }

    /**
     * 画像ファイルのバックアップ
     */
    public function backupImages(): ?string
    {
        try {
            // バックアップディレクトリの作成
            $imagesBackupDir = $this->backupPath . '/images';
            if (!file_exists($imagesBackupDir)) {
                mkdir($imagesBackupDir, 0755, true);
            }

            // ZIPファイル名の生成
            $filename = 'images_backup_' . date('Ymd_His') . '.zip';
            $zipPath = $imagesBackupDir . '/' . $filename;

            // 一時ディレクトリの作成（PharData用）
            $tempDir = sys_get_temp_dir() . '/backup_' . uniqid();
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // ストレージディレクトリを一時ディレクトリにコピー
            $this->copyDirectory($this->imagesPath, $tempDir . '/public');

            // ZIPファイルの作成
            if (!$this->createZipFromDirectory($tempDir, $zipPath)) {
                // 一時ディレクトリの削除
                $this->removeDirectory($tempDir);
                throw new Exception('ZIPファイルの作成に失敗しました。');
            }

            // 一時ディレクトリの削除
            $this->removeDirectory($tempDir);

            // 古いバックアップの削除
            $this->cleanOldBackups('images');

            Log::info('画像バックアップが完了しました: ' . $filename);

            return $filename;
        } catch (Exception $e) {
            Log::error('画像バックアップエラー: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * データベースのバックアップ
     */
    public function backupDatabase(): ?string
    {
        try {
            // バックアップディレクトリの作成
            $databaseBackupDir = $this->backupPath . '/database';
            if (!file_exists($databaseBackupDir)) {
                mkdir($databaseBackupDir, 0755, true);
            }

            // SQLファイル名の生成
            $filename = 'database_backup_' . date('Ymd_His') . '.sql';
            $sqlPath = $databaseBackupDir . '/' . $filename;

            // データベース設定の取得
            $connection = config('database.default');
            $config = config("database.connections.{$connection}");

            // mysqldumpコマンドの構築
            $command = sprintf(
                'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s 2>&1',
                escapeshellarg($config['username']),
                escapeshellarg($config['password']),
                escapeshellarg($config['host']),
                escapeshellarg($config['port'] ?? '3306'),
                escapeshellarg($config['database']),
                escapeshellarg($sqlPath)
            );

            // コマンドの実行
            exec($command, $output, $returnVar);

            if ($returnVar !== 0 || !file_exists($sqlPath)) {
                throw new Exception('データベースバックアップの実行に失敗しました');
            }

            // 古いバックアップの削除
            $this->cleanOldBackups('database');

            Log::info('データベースバックアップが完了しました: ' . $filename);

            return $filename;
        } catch (Exception $e) {
            Log::error('データベースバックアップエラー: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * ディレクトリをコピー（再帰的）
     */
    protected function copyDirectory(string $source, string $destination): void
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $files = scandir($source);
        
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $sourcePath = $source . '/' . $file;
            $destPath = $destination . '/' . $file;

            if (is_dir($sourcePath)) {
                $this->copyDirectory($sourcePath, $destPath);
            } else {
                copy($sourcePath, $destPath);
            }
        }
    }

    /**
     * ディレクトリを削除（再帰的）
     */
    protected function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        
        foreach ($files as $file) {
            $filePath = $dir . '/' . $file;
            
            if (is_dir($filePath)) {
                $this->removeDirectory($filePath);
            } else {
                unlink($filePath);
            }
        }
        
        rmdir($dir);
    }

    /**
     * ディレクトリからZIPファイルを作成（外部zipコマンドまたはPharDataを使用）
     */
    protected function createZipFromDirectory(string $sourceDir, string $zipPath): bool
    {
        // まず外部zipコマンドを試す（Windows環境で7-Zipなどがインストールされている場合）
        $zipCommand = $this->findZipCommand();
        
        if ($zipCommand) {
            // 既存のZIPファイルを削除
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }

            // 外部zipコマンドを使用
            if (strpos($zipCommand, '7z') !== false) {
                // 7-Zipの場合
                $command = sprintf(
                    '%s a -tzip %s %s\*',
                    escapeshellarg($zipCommand),
                    escapeshellarg($zipPath),
                    escapeshellarg($sourceDir)
                );
            } else {
                // 標準zipコマンドの場合
                $command = sprintf(
                    'cd %s && %s -r %s .',
                    escapeshellarg($sourceDir),
                    escapeshellarg($zipCommand),
                    escapeshellarg($zipPath)
                );
            }
            
            exec($command . ' 2>&1', $output, $returnVar);
            
            if ($returnVar === 0 && file_exists($zipPath)) {
                return true;
            }
        }

        // 外部zipコマンドが利用できない場合は、PharDataで直接ZIP作成を試す
        try {
            // 既存のZIPファイルを削除
            if (file_exists($zipPath)) {
                unlink($zipPath);
            }

            // PharDataでZIPファイルを直接作成
            // Phar::ZIP定数を使用して明示的にZIP形式を指定
            $phar = new PharData($zipPath, 0, null, Phar::ZIP);
            $phar->buildFromDirectory($sourceDir);
            
            if (file_exists($zipPath)) {
                return true;
            }
        } catch (Exception $e) {
            Log::error('ZIP作成エラー（PharData）: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * 利用可能なzipコマンドを検索
     */
    protected function findZipCommand(): ?string
    {
        // Windows環境で一般的なzipコマンドのパス
        $possiblePaths = [
            'zip',  // PATHに含まれている場合
            'C:\\Program Files\\7-Zip\\7z.exe',  // 7-Zip
            'C:\\Program Files (x86)\\7-Zip\\7z.exe',  // 7-Zip (32bit)
        ];

        foreach ($possiblePaths as $path) {
            if ($path === 'zip') {
                // PATHからzipコマンドを探す
                exec('where zip 2>nul', $output, $returnVar);
                if ($returnVar === 0 && !empty($output)) {
                    return 'zip';
                }
            } else {
                // 7-Zipのパスを確認
                if (file_exists($path)) {
                    return $path;
                }
            }
        }

        return null;
    }

    /**
     * 古いバックアップファイルの削除
     */
    protected function cleanOldBackups(string $type): void
    {
        $backupDir = $this->backupPath . '/' . $type;
        
        if (!is_dir($backupDir)) {
            return;
        }

        // ファイル一覧の取得
        $files = glob($backupDir . '/*');
        
        // 更新日時でソート（新しい順）
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        // 7世代を超えるファイルを削除
        if (count($files) > $this->retentionDays) {
            $filesToDelete = array_slice($files, $this->retentionDays);
            
            foreach ($filesToDelete as $file) {
                if (is_file($file)) {
                    unlink($file);
                    Log::info('古いバックアップファイルを削除しました: ' . basename($file));
                }
            }
        }
    }

    /**
     * バックアップファイル一覧の取得
     */
    public function getBackupList(string $type): array
    {
        $backupDir = $this->backupPath . '/' . $type;
        $files = [];

        if (!is_dir($backupDir)) {
            return $files;
        }

        $fileList = glob($backupDir . '/*');
        
        foreach ($fileList as $file) {
            if (is_file($file)) {
                $files[] = [
                    'filename' => basename($file),
                    'size' => filesize($file),
                    'created_at' => date('Y-m-d H:i:s', filemtime($file)),
                    'size_human' => $this->formatBytes(filesize($file)),
                ];
            }
        }

        // 作成日時でソート（新しい順）
        usort($files, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return $files;
    }

    /**
     * バックアップファイルのパス取得
     */
    public function getBackupPath(string $type, string $filename): ?string
    {
        $filePath = $this->backupPath . '/' . $type . '/' . $filename;
        
        if (file_exists($filePath) && is_file($filePath)) {
            return $filePath;
        }

        return null;
    }

    /**
     * ファイルサイズのフォーマット
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

