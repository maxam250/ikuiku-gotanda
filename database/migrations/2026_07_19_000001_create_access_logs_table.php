<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            // UU判定用の訪問者識別子（1stパーティCookie）
            $table->char('visitor_id', 32);
            // アクセスパス（クエリ除く）
            $table->string('path', 191);
            // リファラ（生・全文）
            $table->string('referer', 255)->nullable();
            // リファラのホスト（集計用・自サイトからの遷移はnull）
            $table->string('referer_host', 191)->nullable();
            // アクセス日時（範囲抽出・自動削除・UU/PV集計を1本の複合indexでカバー）
            $table->timestamp('created_at')->nullable();

            $table->index(['created_at', 'visitor_id'], 'access_logs_created_visitor_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_logs');
    }
};
