<x-admin-layout>
    <x-slot name="header">
        サーバー設定
    </x-slot>

    <div class="back">
        <a href="{{ route('admin.dashboard') }}" class="btn-primary">戻る</a>
    </div>

    <section class="MessageArea">
        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
    </section>

    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            
            <!-- 現在の接続状況 -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">現在の接続状況</h2>
                <div id="connection-status" class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="font-medium">外部サーバー接続:</span>
                        <span id="external-status" class="px-3 py-1 rounded-full text-sm font-medium"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-medium">現在のモデル:</span>
                        <span id="current-model" class="text-sm text-gray-600"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-medium">接続名:</span>
                        <span id="connection-name" class="text-sm text-gray-600"></span>
                    </div>
                </div>
                
                <div class="mt-6 space-y-3">
                    <button id="test-connection" class="af-btn af-btn-search af-btn-full">
                        外部サーバー接続テスト
                    </button>
                    <button id="enable-external" class="af-btn af-btn-primary af-btn-full">
                        外部サーバー接続を有効化
                    </button>
                    <button id="disable-external" class="af-btn af-btn-delete af-btn-full">
                        外部サーバー接続を無効化
                    </button>
                </div>
            </div>

            <!-- 外部サーバー設定 -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold mb-4">外部サーバー設定</h2>
                <form id="settings-form" class="space-y-4">
                    @csrf
                    <div>
                        <label for="external_host" class="block text-sm font-medium text-gray-700">ホスト</label>
                        <input type="text" id="external_host" name="external_host" value="{{ $currentSettings['external_host'] }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="external_port" class="block text-sm font-medium text-gray-700">ポート</label>
                        <input type="number" id="external_port" name="external_port" value="{{ $currentSettings['external_port'] }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="external_database" class="block text-sm font-medium text-gray-700">データベース名</label>
                        <input type="text" id="external_database" name="external_database" value="{{ $currentSettings['external_database'] }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="external_username" class="block text-sm font-medium text-gray-700">ユーザー名</label>
                        <input type="text" id="external_username" name="external_username" value="{{ $currentSettings['external_username'] }}" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="external_password" class="block text-sm font-medium text-gray-700">パスワード</label>
                        <input type="password" id="external_password" name="external_password" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                               placeholder="変更する場合のみ入力">
                    </div>
                    
                    <button type="submit" class="af-btn af-btn-primary af-btn-full">
                        設定を更新
                    </button>
                </form>
            </div>
        </div>

        <!-- ローカルサーバー情報 -->
        <div class="mt-8 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4">ローカルサーバー情報</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <span class="font-medium">ホスト:</span>
                    <span class="ml-2">{{ $currentSettings['local_host'] }}</span>
                </div>
                <div>
                    <span class="font-medium">ポート:</span>
                    <span class="ml-2">{{ $currentSettings['local_port'] }}</span>
                </div>
                <div>
                    <span class="font-medium">データベース名:</span>
                    <span class="ml-2">{{ $currentSettings['local_database'] }}</span>
                </div>
                <div>
                    <span class="font-medium">ユーザー名:</span>
                    <span class="ml-2">{{ $currentSettings['local_username'] }}</span>
                </div>
            </div>
        </div>

        <!-- テスト結果表示エリア -->
        <div id="test-result" class="mt-8 bg-white rounded-lg shadow-md p-6 hidden">
            <h2 class="text-xl font-bold mb-4">テスト結果</h2>
            <div id="test-message" class="p-4 rounded-md"></div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 接続状況を更新
            updateConnectionStatus();
            
            // 接続テストボタン
            document.getElementById('test-connection').addEventListener('click', function() {
                this.disabled = true;
                this.textContent = 'テスト中...';
                
                fetch('{{ route("admin.settings.test-connection") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    showTestResult(data.success, data.message);
                    this.disabled = false;
                    this.textContent = '外部サーバー接続テスト';
                })
                .catch(error => {
                    showTestResult(false, 'エラーが発生しました: ' + error.message);
                    this.disabled = false;
                    this.textContent = '外部サーバー接続テスト';
                });
            });
            
            // 有効化ボタン
            document.getElementById('enable-external').addEventListener('click', function() {
                if (confirm('外部サーバー接続を有効化しますか？')) {
                    this.disabled = true;
                    this.textContent = '有効化中...';
                    
                    fetch('{{ route("admin.settings.enable") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        showTestResult(data.success, data.message);
                        updateConnectionStatus();
                        this.disabled = false;
                        this.textContent = '外部サーバー接続を有効化';
                    })
                    .catch(error => {
                        showTestResult(false, 'エラーが発生しました: ' + error.message);
                        this.disabled = false;
                        this.textContent = '外部サーバー接続を有効化';
                    });
                }
            });
            
            // 無効化ボタン
            document.getElementById('disable-external').addEventListener('click', function() {
                if (confirm('外部サーバー接続を無効化しますか？')) {
                    this.disabled = true;
                    this.textContent = '無効化中...';
                    
                    fetch('{{ route("admin.settings.disable") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Content-Type': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        showTestResult(data.success, data.message);
                        updateConnectionStatus();
                        this.disabled = false;
                        this.textContent = '外部サーバー接続を無効化';
                    })
                    .catch(error => {
                        showTestResult(false, 'エラーが発生しました: ' + error.message);
                        this.disabled = false;
                        this.textContent = '外部サーバー接続を無効化';
                    });
                }
            });
            
            // 設定更新フォーム
            document.getElementById('settings-form').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                const data = Object.fromEntries(formData);
                
                fetch('{{ route("admin.settings.update") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    showTestResult(data.success, data.message);
                })
                .catch(error => {
                    showTestResult(false, 'エラーが発生しました: ' + error.message);
                });
            });
            
            function updateConnectionStatus() {
                fetch('{{ route("admin.settings.status") }}', {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const statusElement = document.getElementById('external-status');
                    const modelElement = document.getElementById('current-model');
                    const connectionElement = document.getElementById('connection-name');
                    
                    if (data.external_enabled) {
                        statusElement.textContent = '有効';
                        statusElement.className = 'px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800';
                    } else {
                        statusElement.textContent = '無効';
                        statusElement.className = 'px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800';
                    }
                    
                    modelElement.textContent = data.current_model;
                    connectionElement.textContent = data.connection_name;
                })
                .catch(error => {
                    console.error('接続状況の取得に失敗しました:', error);
                });
            }
            
            function showTestResult(success, message) {
                const resultDiv = document.getElementById('test-result');
                const messageDiv = document.getElementById('test-message');
                
                resultDiv.classList.remove('hidden');
                messageDiv.textContent = message;
                
                if (success) {
                    messageDiv.className = 'p-4 rounded-md bg-green-100 text-green-800';
                } else {
                    messageDiv.className = 'p-4 rounded-md bg-red-100 text-red-800';
                }
                
                // 5秒後に非表示
                setTimeout(() => {
                    resultDiv.classList.add('hidden');
                }, 5000);
            }
        });
    </script>
</x-admin-layout> 