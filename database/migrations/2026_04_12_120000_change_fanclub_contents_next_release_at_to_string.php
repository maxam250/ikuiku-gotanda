<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * 次回投稿予定日は公開ロジックに使わないメモのため、日時型から文字列へ変更する。
     */
    public function up(): void
    {
        $memoById = [];
        foreach (DB::table('fanclub_contents')->select('id', 'next_release_at')->cursor() as $row) {
            if ($row->next_release_at === null) {
                $memoById[(int) $row->id] = null;

                continue;
            }
            try {
                $memoById[(int) $row->id] = \Carbon\Carbon::parse($row->next_release_at)
                    ->timezone(config('app.timezone'))
                    ->format('Y-m-d H:i');
            } catch (\Throwable) {
                $memoById[(int) $row->id] = (string) $row->next_release_at;
            }
        }

        Schema::table('fanclub_contents', function (Blueprint $table) {
            $table->dropColumn('next_release_at');
        });

        Schema::table('fanclub_contents', function (Blueprint $table) {
            $table->string('next_release_at', 512)->nullable();
        });

        foreach ($memoById as $id => $memo) {
            DB::table('fanclub_contents')->where('id', $id)->update(['next_release_at' => $memo]);
        }
    }

    public function down(): void
    {
        $tsById = [];
        foreach (DB::table('fanclub_contents')->select('id', 'next_release_at')->cursor() as $row) {
            if ($row->next_release_at === null || $row->next_release_at === '') {
                $tsById[(int) $row->id] = null;

                continue;
            }
            try {
                $tsById[(int) $row->id] = \Carbon\Carbon::parse($row->next_release_at);
            } catch (\Throwable) {
                $tsById[(int) $row->id] = null;
            }
        }

        Schema::table('fanclub_contents', function (Blueprint $table) {
            $table->dropColumn('next_release_at');
        });

        Schema::table('fanclub_contents', function (Blueprint $table) {
            $table->timestamp('next_release_at')->nullable();
        });

        foreach ($tsById as $id => $dt) {
            DB::table('fanclub_contents')->where('id', $id)->update([
                'next_release_at' => $dt,
            ]);
        }
    }
};
