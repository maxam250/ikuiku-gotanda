<x-item-layout>
<link rel="stylesheet" href="{{ asset('css/form.css') }}">

<x-slot name="header">
クーポン設定
</x-slot>

<section class="MessageArea">
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</section>



<section class="coupon-settings-page">
    <h3 class="mb-2">反映させたいクーポンを設定できます。（報酬は割引後の料金からパーセンテージで計算されます）</h3>
    <p class="form-hint">※チェックをON/OFFで即時反映されます</p>
    
    @if($coupons->isNotEmpty())
        <div class="coupon-settings">
            @foreach($coupons as $coupon)
            <label class="coupon-item">
                <input type="checkbox" name="coupons[]" value="{{ $coupon->id }}"
                    {{ in_array($coupon->id, $selectedCoupons) ? 'checked' : '' }}
                    class="coupon-checkbox" data-coupon-id="{{ $coupon->id }}">
                <div class="coupon-info">
                    <div class="coupon-title">{{ $coupon->title }}</div>
                    @if($coupon->code)
                        <div class="coupon-code">コード: {{ $coupon->code }}</div>
                    @endif
                    @if($coupon->description)
                        <div class="coupon-description">{{ $coupon->description }}</div>
                    @endif
                    @if($coupon->expires_at)
                        <div class="coupon-expires">期限: {{ $coupon->expires_at->format('Y/m/d') }}</div>
                    @endif
                </div>
            </label>
            @endforeach
        </div>
    @else
        <div class="no-coupons">
            <p>利用可能なクーポンがありません。</p>
        </div>
    @endif
</section>

<script>
// クーポン設定の即時反映
document.addEventListener('DOMContentLoaded', function() {
    const couponCheckboxes = document.querySelectorAll('.coupon-checkbox');
    
    couponCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const couponId = this.dataset.couponId;
            const isChecked = this.checked;
            
            // 選択されたクーポンのIDを取得
            const selectedCoupons = Array.from(document.querySelectorAll('.coupon-checkbox:checked'))
                .map(cb => cb.value);
            
            // サーバーに送信
            fetch('/item/items/update-coupons', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    coupons: selectedCoupons
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log('クーポン設定が更新されました');
                    // 成功メッセージを表示
                    showMessage('クーポン設定が更新されました', 'success');
                } else {
                    console.error('クーポン設定の更新に失敗しました');
                    // チェックボックスの状態を元に戻す
                    this.checked = !isChecked;
                    showMessage('クーポン設定の更新に失敗しました', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // チェックボックスの状態を元に戻す
                this.checked = !isChecked;
                showMessage('エラーが発生しました', 'error');
            });
        });
    });
});

// メッセージ表示関数
function showMessage(message, type) {
    const messageArea = document.querySelector('.MessageArea');
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `alert ${alertClass}`;
    messageDiv.textContent = message;
    
    messageArea.appendChild(messageDiv);
    
    // 3秒後にメッセージを削除
    setTimeout(() => {
        messageDiv.remove();
    }, 3000);
}
</script>

<style>
.coupon-settings-page {
    margin: 0 auto;
    padding: 20px;
}

.coupon-settings-page h2 {
    color: #333;
    margin-bottom: 10px;
    font-size: 24px;
}

.form-hint {
    color: #666;
    margin-bottom: 30px;
    font-style: italic;
}

.coupon-settings {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.coupon-item {
    display: flex;
    align-items: flex-start;
    padding: 20px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.coupon-item:hover {
    border-color: #3b82f6;
    background-color: #f8fafc;
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.coupon-item input[type="checkbox"] {
    margin-right: 15px;
    margin-top: 3px;
    transform: scale(1.3);
    accent-color: #10b981;
}

.coupon-info {
    flex: 1;
}

.coupon-title {
    font-weight: bold;
    font-size: 18px;
    color: #1f2937;
    margin-bottom: 8px;
}

.coupon-code {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 5px;
    font-family: monospace;
    background: #f3f4f6;
    padding: 2px 6px;
    border-radius: 4px;
    display: inline-block;
}

.coupon-description {
    font-size: 14px;
    color: #4b5563;
    margin-bottom: 5px;
    line-height: 1.5;
}

.coupon-expires {
    font-size: 12px;
    color: #ef4444;
    font-weight: 500;
    background: #fef2f2;
    padding: 2px 6px;
    border-radius: 4px;
    display: inline-block;
}

.coupon-item:has(input:checked) {
    border-color: #10b981;
    background-color: #ecfdf5;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
}

.coupon-item:has(input:checked) .coupon-title {
    color: #065f46;
}

.no-coupons {
    text-align: center;
    padding: 60px 20px;
    color: #6b7280;
}

.no-coupons p {
    font-size: 18px;
}

.back {
    margin-bottom: 20px;
}

.back .btn-primary {
    display: inline-flex;
    align-items: center;
    padding: 10px 20px;
    background-color: #3b82f6;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    transition: background-color 0.3s ease;
}

.back .btn-primary:hover {
    background-color: #2563eb;
}

.alert {
    padding: 12px 16px;
    border-radius: 6px;
    margin-bottom: 15px;
    border: 1px solid;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border-color: #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border-color: #f5c6cb;
}
</style>

</x-item-layout>

