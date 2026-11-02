<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    DB::statement('ALTER TABLE members ADD UNIQUE members_email_unique (email(191));');
    Schema::table('members', function (Blueprint $table) {
        $table->unique('loginid');
    });
}

public function down()
{
    Schema::table('members', function (Blueprint $table) {
        $table->dropUnique('members_email_unique');
        $table->dropUnique(['loginid']);
    });
}
};
