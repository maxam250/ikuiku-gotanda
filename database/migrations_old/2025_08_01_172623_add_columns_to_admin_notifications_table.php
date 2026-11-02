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
        Schema::table('admin_notifications', function (Blueprint $table) {
            if (!Schema::hasColumn('admin_notifications', 'type')) {
                $table->string('type')->after('id'); // member_message, item_message, attendance_request, new_reservation
            }
            if (!Schema::hasColumn('admin_notifications', 'related_id')) {
                $table->unsignedBigInteger('related_id')->after('type'); // 関連するレコードのID
            }
            if (!Schema::hasColumn('admin_notifications', 'title')) {
                $table->string('title')->after('related_id');
            }
            if (!Schema::hasColumn('admin_notifications', 'message')) {
                $table->text('message')->after('title');
            }
            if (!Schema::hasColumn('admin_notifications', 'sender')) {
                $table->string('sender')->after('message');
            }
            if (!Schema::hasColumn('admin_notifications', 'color')) {
                $table->string('color')->after('sender'); // blue, purple, orange, green
            }
            if (!Schema::hasColumn('admin_notifications', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('color');
            }
            if (!Schema::hasColumn('admin_notifications', 'read_at')) {
                $table->timestamp('read_at')->nullable()->after('is_read');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admin_notifications', function (Blueprint $table) {
            $table->dropColumn(['type', 'related_id', 'title', 'message', 'sender', 'color', 'is_read', 'read_at']);
        });
    }
};
