<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fanclub_memberships', function (Blueprint $table) {
            $table->timestamp('warning_sent_at')->nullable()->after('admin_note');
            $table->unsignedTinyInteger('warning_count')->default(0)->after('warning_sent_at');
            $table->timestamp('suspended_until')->nullable()->after('warning_count');
            $table->text('suspension_reason')->nullable()->after('suspended_until');
        });
    }

    public function down(): void
    {
        Schema::table('fanclub_memberships', function (Blueprint $table) {
            $table->dropColumn(['warning_sent_at', 'warning_count', 'suspended_until', 'suspension_reason']);
        });
    }
};
