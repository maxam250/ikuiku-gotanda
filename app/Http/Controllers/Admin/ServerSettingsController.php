<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\ExternalDatabaseService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class ServerSettingsController extends Controller
{
    /**
     * 設定ページを表示
     */
    public function index()
    {
        // 現在の設定を取得
        $currentSettings = [
            'external_enabled' => config('database.external_enabled', false),
            'external_host' => config('database.connections.external_mysql.host'),
            'external_port' => config('database.connections.external_mysql.port'),
            'external_database' => config('database.connections.external_mysql.database'),
            'external_username' => config('database.connections.external_mysql.username'),
            'local_host' => config('database.connections.mysql.host'),
            'local_port' => config('database.connections.mysql.port'),
            'local_database' => config('database.connections.mysql.database'),
            'local_username' => config('database.connections.mysql.username'),
        ];

        return view('admin.settings.server', compact('currentSettings'));
    }

    /**
     * 外部サーバー接続テスト
     */
    public function testExternalConnection()
    {
        try {
            $result = Artisan::call('external:test-connection');
            $output = Artisan::output();
            
            return response()->json([
                'success' => $result === 0,
                'message' => $output,
                'result' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '接続テスト中にエラーが発生しました: ' . $e->getMessage(),
                'result' => -1
            ]);
        }
    }

    /**
     * 外部サーバー接続を有効化
     */
    public function enableExternal()
    {
        // 本番環境では有効化を制限
        if (app()->environment('production')) {
            return response()->json([
                'success' => false,
                'message' => '本番環境では有効化はできません。.envファイルでEXTERNAL_DB_ENABLED=trueを設定してください。',
                'result' => -1
            ]);
        }

        try {
            $result = Artisan::call('external:enable');
            $output = Artisan::output();
            
            // 設定キャッシュをクリア
            Artisan::call('config:clear');
            
            return response()->json([
                'success' => $result === 0,
                'message' => $output,
                'result' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '有効化中にエラーが発生しました: ' . $e->getMessage(),
                'result' => -1
            ]);
        }
    }

    /**
     * 外部サーバー接続を無効化
     */
    public function disableExternal()
    {
        // 本番環境では無効化を制限
        if (app()->environment('production')) {
            return response()->json([
                'success' => false,
                'message' => '本番環境では無効化はできません。.envファイルでEXTERNAL_DB_ENABLED=falseを設定してください。',
                'result' => -1
            ]);
        }

        try {
            $result = Artisan::call('external:disable');
            $output = Artisan::output();
            
            // 設定キャッシュをクリア
            Artisan::call('config:clear');
            
            return response()->json([
                'success' => $result === 0,
                'message' => $output,
                'result' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '無効化中にエラーが発生しました: ' . $e->getMessage(),
                'result' => -1
            ]);
        }
    }

    /**
     * 設定を更新
     */
    public function updateSettings(Request $request)
    {
        // 本番環境では設定変更を制限
        if (app()->environment('production')) {
            return response()->json([
                'success' => false,
                'message' => '本番環境では設定の変更はできません。.envファイルを直接編集してください。'
            ]);
        }

        $request->validate([
            'external_host' => 'required|string',
            'external_port' => 'required|integer',
            'external_database' => 'required|string',
            'external_username' => 'required|string',
            'external_password' => 'nullable|string',
        ]);

        try {
            // .envファイルを更新
            $envPath = base_path('.env');
            $envContent = file_get_contents($envPath);

            $envContent = $this->updateEnvValue($envContent, 'EXTERNAL_DB_HOST', $request->external_host);
            $envContent = $this->updateEnvValue($envContent, 'EXTERNAL_DB_PORT', $request->external_port);
            $envContent = $this->updateEnvValue($envContent, 'EXTERNAL_DB_DATABASE', $request->external_database);
            $envContent = $this->updateEnvValue($envContent, 'EXTERNAL_DB_USERNAME', $request->external_username);
            
            if ($request->external_password) {
                $envContent = $this->updateEnvValue($envContent, 'EXTERNAL_DB_PASSWORD', $request->external_password);
            }

            file_put_contents($envPath, $envContent);

            // 設定キャッシュをクリア
            Artisan::call('config:clear');

            return response()->json([
                'success' => true,
                'message' => '設定が更新されました。'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => '設定の更新中にエラーが発生しました: ' . $e->getMessage()
            ]);
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

    /**
     * 現在の接続状況を取得
     */
    public function getConnectionStatus()
    {
        $externalEnabled = config('database.external_enabled', false);
        $externalModel = ExternalDatabaseService::getReservationModel();
        
        return response()->json([
            'external_enabled' => $externalEnabled,
            'current_model' => get_class($externalModel),
            'connection_name' => $externalModel->getConnectionName()
        ]);
    }
} 