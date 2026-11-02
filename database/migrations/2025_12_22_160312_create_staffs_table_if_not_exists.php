<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // staffsテーブルが存在しない場合のみ作成
        if (!Schema::hasTable('staffs')) {
            Schema::create('staffs', function (Blueprint $table) {
                $table->id();
                $table->string('name')->comment('スタッフ名');
                $table->enum('role', ['photographer', 'assistant', 'makeup', 'stylist', 'reception', 'manager', 'other'])->default('other')->comment('役職・担当');
                $table->enum('employment_type', ['full_time', 'part_time', 'contract', 'freelance', 'other'])->default('other')->comment('雇用形態');
                $table->time('default_start_time')->nullable()->comment('基本開始時間');
                $table->time('default_end_time')->nullable()->comment('基本終了時間');
                $table->json('work_days')->nullable()->comment('基本出勤曜日 [1,2,3,4,5,6,7] 1=月曜日');
                $table->unsignedBigInteger('default_studio_id')->nullable()->comment('基本勤務スタジオID');
                $table->json('skills')->nullable()->comment('スキル（JSON形式）');
                $table->string('specialties')->nullable()->comment('専門分野');
                $table->integer('experience')->nullable()->comment('経験年数');
                $table->boolean('is_active')->default(true)->comment('アクティブ状態');
                $table->text('memo')->nullable()->comment('メモ');
                $table->string('color')->nullable()->comment('カラーコード（スケジュール表示用）');
                $table->integer('sort')->default(0)->comment('ソート順');
                $table->timestamps();
                $table->time('work_start_time')->nullable()->comment('基本開始時間');
                $table->time('work_end_time')->nullable()->comment('基本終了時間');
                $table->boolean('auto_schedule_enabled')->default(false)->comment('自動スケジュール生成有効');
                $table->date('auto_schedule_start_date')->nullable()->comment('自動生成開始日');
                $table->date('auto_schedule_end_date')->nullable()->comment('自動生成終了日');
                $table->json('weekly_schedule')->nullable()->comment('曜日ごとの勤務スケジュール設定');
            });

            // staffsテーブルのインデックス
            Schema::table('staffs', function (Blueprint $table) {
                $table->index(['is_active', 'role'], 'staffs_is_active_role_index');
                $table->index('default_studio_id', 'staffs_default_studio_id_index');
                $table->index('auto_schedule_enabled', 'staffs_auto_schedule_enabled_index');
            });

            // staffsテーブルの外部キー制約（equipmentsテーブルが存在する場合のみ）
            if (Schema::hasTable('equipments')) {
                Schema::table('staffs', function (Blueprint $table) {
                    $table->foreign('default_studio_id', 'staffs_default_studio_id_foreign')
                        ->references('id')
                        ->on('equipments')
                        ->onDelete('set null');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('staffs')) {
            Schema::table('staffs', function (Blueprint $table) {
                $table->dropForeign('staffs_default_studio_id_foreign');
            });
            Schema::dropIfExists('staffs');
        }
    }
};
