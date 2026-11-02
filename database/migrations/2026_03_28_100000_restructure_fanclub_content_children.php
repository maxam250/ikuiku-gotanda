<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fanclub_content_children', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fanclub_content_id')->constrained('fanclub_contents')->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->boolean('teaser_enabled')->default(false);
            $table->string('teaser_style', 20)->default('blur'); // blur | custom_image
            $table->string('teaser_image_path')->nullable();
            $table->timestamp('teaser_reveal_at')->nullable();
            $table->text('teaser_promo_note')->nullable();
            $table->timestamps();

            $table->index(['fanclub_content_id', 'sort']);
            $table->index(['fanclub_content_id', 'published_at']);
            $table->index(['scheduled_at', 'published_at']);
        });

        Schema::create('fanclub_content_child_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fanclub_content_child_id')->constrained('fanclub_content_children')->cascadeOnDelete();
            $table->string('type', 20);
            $table->json('value');
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();

            $table->index(['fanclub_content_child_id', 'sort']);
            $table->index('type');
        });

        $now = now();
        $grouped = DB::table('fanclub_content_items')
            ->orderBy('content_id')
            ->orderBy('sort')
            ->get()
            ->groupBy('content_id');

        foreach ($grouped as $contentId => $rows) {
            $parent = DB::table('fanclub_contents')->where('id', $contentId)->first();
            if (!$parent) {
                continue;
            }

            $childId = DB::table('fanclub_content_children')->insertGetId([
                'fanclub_content_id' => (int) $contentId,
                'title' => null,
                'description' => null,
                'sort' => 0,
                'scheduled_at' => null,
                'published_at' => $parent->published_at,
                'teaser_enabled' => false,
                'teaser_style' => 'blur',
                'teaser_image_path' => null,
                'teaser_reveal_at' => null,
                'teaser_promo_note' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($rows as $row) {
                $value = $row->value;
                if (is_array($value) || is_object($value)) {
                    $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                }

                DB::table('fanclub_content_child_items')->insert([
                    'fanclub_content_child_id' => $childId,
                    'type' => $row->type,
                    'value' => $value,
                    'sort' => (int) $row->sort,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        Schema::dropIfExists('fanclub_content_items');
    }

    public function down(): void
    {
        Schema::create('fanclub_content_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_id')->constrained('fanclub_contents')->cascadeOnDelete();
            $table->string('type', 20);
            $table->json('value');
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();

            $table->index(['content_id', 'sort']);
            $table->index('type');
        });

        $now = now();
        $children = DB::table('fanclub_content_children')->orderBy('fanclub_content_id')->orderBy('sort')->get();

        foreach ($children as $child) {
            $items = DB::table('fanclub_content_child_items')
                ->where('fanclub_content_child_id', $child->id)
                ->orderBy('sort')
                ->get();

            foreach ($items as $item) {
                $value = $item->value;
                if (is_array($value) || is_object($value)) {
                    $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                }

                DB::table('fanclub_content_items')->insert([
                    'content_id' => $child->fanclub_content_id,
                    'type' => $item->type,
                    'value' => $value,
                    'sort' => $item->sort,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        Schema::dropIfExists('fanclub_content_child_items');
        Schema::dropIfExists('fanclub_content_children');
    }
};
