<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    protected $backupService;

    public function __construct(BackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    /**
     * バックアップ一覧表示
     */
    public function index()
    {
        $imageBackups = $this->backupService->getBackupList('images');
        $databaseBackups = $this->backupService->getBackupList('database');

        return view('admin.backup.index', [
            'imageBackups' => $imageBackups,
            'databaseBackups' => $databaseBackups,
        ]);
    }

    /**
     * 手動バックアップ実行
     */
    public function create(Request $request)
    {
        $type = $request->input('type', 'all');

        try {
            if ($type === 'all' || $type === 'images') {
                $this->backupService->backupImages();
            }

            if ($type === 'all' || $type === 'database') {
                $this->backupService->backupDatabase();
            }

            return redirect()->route('admin.backup.index')
                ->with('success', 'バックアップが正常に作成されました。');
        } catch (\Exception $e) {
            return redirect()->route('admin.backup.index')
                ->with('error', 'バックアップの作成に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * S3 手動バックアップ実行（backup.sh をバックグラウンドで起動）
     */
    public function s3Backup()
    {
        $scriptPath = base_path('scripts/backup.sh');

        if (!file_exists($scriptPath)) {
            return redirect()->route('admin.backup.index')
                ->with('error', 'バックアップスクリプトが見つかりません: ' . $scriptPath);
        }

        exec('bash ' . escapeshellarg($scriptPath) . ' >> /var/log/backup.log 2>&1 &');

        return redirect()->route('admin.backup.index')
            ->with('success', 'S3バックアップをバックグラウンドで開始しました。進捗は /var/log/backup.log で確認できます。');
    }

    /**
     * S3 手動リストア実行（restore.sh をバックグラウンドで起動）
     */
    public function s3Restore()
    {
        $scriptPath = base_path('scripts/restore.sh');

        if (!file_exists($scriptPath)) {
            return redirect()->route('admin.backup.index')
                ->with('error', '復元スクリプトが見つかりません: ' . $scriptPath);
        }

        exec('bash ' . escapeshellarg($scriptPath) . ' >> /var/log/restore.log 2>&1 &');

        return redirect()->route('admin.backup.index')
            ->with('success', 'S3からの復元をバックグラウンドで開始しました。進捗は /var/log/restore.log で確認できます。');
    }

    /**
     * バックアップファイルのダウンロード
     */
    public function download(string $type, string $filename)
    {
        // タイプの検証
        if (!in_array($type, ['images', 'database'])) {
            abort(404);
        }

        // ファイルパスの取得
        $filePath = $this->backupService->getBackupPath($type, $filename);

        if (!$filePath || !file_exists($filePath)) {
            abort(404, 'バックアップファイルが見つかりません。');
        }

        // ファイルのダウンロード
        return response()->download($filePath, $filename, [
            'Content-Type' => $type === 'images' ? 'application/zip' : 'application/sql',
        ]);
    }

    /**
     * バックアップファイルの削除
     */
    public function destroy(Request $request, string $type, string $filename)
    {
        // タイプの検証
        if (!in_array($type, ['images', 'database'])) {
            abort(404);
        }

        try {
            // ファイルパスの取得
            $filePath = $this->backupService->getBackupPath($type, $filename);

            if (!$filePath || !file_exists($filePath)) {
                return redirect()->route('admin.backup.index')
                    ->with('error', 'バックアップファイルが見つかりません。');
            }

            // ファイルの削除
            if (unlink($filePath)) {
                return redirect()->route('admin.backup.index')
                    ->with('success', 'バックアップファイルを削除しました: ' . $filename);
            } else {
                return redirect()->route('admin.backup.index')
                    ->with('error', 'バックアップファイルの削除に失敗しました。');
            }
        } catch (\Exception $e) {
            return redirect()->route('admin.backup.index')
                ->with('error', 'バックアップファイルの削除に失敗しました: ' . $e->getMessage());
        }
    }
}

