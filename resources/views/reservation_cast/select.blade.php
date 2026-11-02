@extends('layouts.app')

@section('content')
<div class="titleArea">
<h2>RESERVATION</h2>
<div class="title">ログイン</div>
</div>
    <div class="reservation-select">
        <h2 class="select-title">会員の方はログインしてください</h2>

        <div class="select-container">
            <div class="select-box member">
                <h3>会員の方</h3>
                <p>会員情報を使用して予約ができます</p>
                <div class="btn-group">
                    @if(Auth::guard('member')->check())
                        <a href="{{ route('reservation.index') }}" class="btn primary">会員として予約する</a>
                    @else
                        <a href="{{ route('member.login') }}?redirect={{ route('reservation.index') }}" class="btn primary">ログインして予約</a>
                        <a href="{{ route('member.register') }}" class="btn secondary">新規会員登録</a>
                    @endif
                </div>
            </div>

            <div class="select-box visitor">
                <h3>ビジターの方</h3>
                <p>会員登録なしで予約ができます</p>
                <div class="btn-group">
                    <a href="{{ route('reservation.select', ['type' => 'visitor']) }}" class="btn primary">ビジターとして予約</a>
                </div>
            </div>
        </div>
    </div>

<style>
.reservation-select {
    max-width: 800px;
    margin: 2rem auto;
    padding: 2rem 1rem;
}

.select-title {
    text-align: center;
    margin-bottom: 2rem;
    font-size: 1.5rem;
    font-weight: bold;
}

.select-container {
    display: flex;
    gap: 2rem;
    justify-content: center;
    flex-wrap: wrap;
}

.select-box {
    flex: 1;
    min-width: 300px;
    padding: 2rem;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.select-box h3 {
    font-size: 1.2rem;
    font-weight: bold;
    margin-bottom: 1rem;
    text-align: center;
}

.select-box p {
    text-align: center;
    margin-bottom: 1.5rem;
    color: #666;
}

.btn-group {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.btn {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 1rem 1.5rem !important;
    text-align: center !important;
    border-radius: 8px !important;
    font-weight: bold !important;
    transition: all 0.3s ease !important;
    text-decoration: none !important;
    font-size: 1rem !important;
    min-height: 3rem !important;
    margin: 0.5rem auto !important;
    width: 100% !important;
    max-width: 300px !important;
}

.btn.primary {
    background: linear-gradient(0deg, #38d1b0, #2ccca9) !important;
    color: white !important;
    box-shadow: 0 4px 15px rgba(51, 61, 59, 0.3) !important;
    border: none !important;
}

.btn.secondary {
    background: linear-gradient(0deg, #f8f9fa, #e9ecef) !important;
    color: #495057 !important;
    border: 2px solid #dee2e6 !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
}

.btn:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 15px rgba(51, 61, 59, 0.3) !important;
}

.btn.secondary:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    background: linear-gradient(135deg, #e9ecef, #dee2e6) !important;
}

@media (max-width: 768px) {
    .select-box {
        min-width: 100%;
        padding: 1.5rem;
    }
    
    .btn {
        padding: 1.25rem 2rem;
        font-size: 1.1rem;
        min-height: 3.5rem;
        margin: 0.5rem 0;
    }
    
    .select-title {
        font-size: 1.3rem;
        margin-bottom: 1.5rem;
    }
    
    .select-box h3 {
        font-size: 1.1rem;
    }
    
    .select-box p {
        font-size: 0.95rem;
    }
}
</style>
@endsection
