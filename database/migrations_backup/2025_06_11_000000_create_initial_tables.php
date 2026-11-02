<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // adminsテーブル
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('login_name')->unique();
            $table->integer('role')->default(1);
            $table->tinyInteger('status')->default(1);
            $table->rememberToken();
            $table->timestamps();
        });

        // デフォルトadminレコードを追加
        DB::table('admins')->insert([
            'name' => 'admin',
            'email' => 'kagawa@1creation.co.jp',
            'password' => Hash::make('password'),
            'login_name' => 'admin',
            'role' => 1,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // membersテーブル
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('email_verified_at')->nullable();
            $table->string('loginid');
            $table->string('password');
            $table->date('birth')->nullable();
            $table->integer('gender')->default(0)->comment('0:未設定 1:男性 2:女性 3:その他');
            $table->integer('kind')->default(1)->comment('会員種別 0:停止 1:通常 2:プレミア');
            $table->string('check')->default(0)->comment('認証 0:未認証 1:認証済み');
            $table->string('tel')->nullable();
            $table->string('line')->nullable();
            $table->string('sns')->nullable();
            $table->string('url')->nullable();
            $table->text('memo')->nullable();
            $table->text('memo_share')->nullable();
            $table->string('remember_token')->nullable();
            $table->string('nickname')->nullable();
            $table->string('member_no')->nullable();
            $table->string('favo')->nullable();
            $table->string('block')->nullable();
            $table->string('filename')->nullable();
            $table->timestamps();
        });

        // member_pointsテーブル
        Schema::create('member_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->integer('point')->default(0);
            $table->enum('status', ['add', 'del']);
            $table->text('memo')->nullable();
            $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
            $table->timestamps();
        });

        // itemsテーブル
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->integer('shop_id')->default(0);
            $table->string('item_name');
            $table->integer('item_active')->default(0);
            $table->integer('item_cate')->default(0);
            $table->integer('item_new')->default(1);
            $table->integer('item_age')->nullable();
            $table->integer('item_height')->nullable();
            $table->integer('item_b')->nullable();
            $table->integer('item_w')->nullable();
            $table->integer('item_h')->nullable();
            $table->string('item_cup')->nullable();
            $table->string('item_shoes')->nullable();
            $table->text('item_comment')->nullable();
            $table->text('item_comment2')->nullable();
            $table->string('item_blogname')->nullable();
            $table->string('item_blog')->nullable();
            $table->string('item_url')->nullable();
            $table->string('item_twitter')->nullable();
            $table->string('item_instagram')->nullable();
            $table->string('item_tiktok')->nullable();
            $table->string('item_youtube')->nullable();
            $table->integer('item_sort')->nullable();
            $table->string('item_line')->nullable();
            $table->string('item_tel')->nullable();
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('loginid')->nullable();
            $table->string('password')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->text('item_p_comment')->nullable();
            $table->dateTime('item_p_comment_date')->nullable();
            $table->timestamps();
        });

        // item_imagesテーブル
        Schema::create('item_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->string('filename');
            $table->integer('flag')->default(0);
            $table->integer('sort')->default(0);
            $table->timestamps();
        });

        // coursesテーブル
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->integer('active')->default(1);
            $table->string('title');
            $table->text('body')->nullable();
            $table->integer('sort')->default(0);
            $table->timestamps();
        });

        // course_feesテーブル
        Schema::create('course_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->string('course')->nullable();
            $table->integer('course_time')->nullable();
            $table->integer('fee')->default(0);
            $table->text('text')->nullable();
            $table->integer('sort')->default(0);
            $table->timestamps();
        });

        // eventsテーブル
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->integer('active')->default(0);
            $table->string('title');
            $table->text('body')->nullable();
            $table->integer('sort')->default(0);
            $table->string('filename')->nullable();
            $table->integer('position')->default(0);
            $table->integer('cate')->default(0);
            $table->string('link')->nullable();
            $table->integer('timer_flag')->default(0);
            $table->dateTime('date_start')->nullable();
            $table->dateTime('date_end')->nullable();
            $table->timestamps();
        });

        // historiesテーブル
        Schema::create('histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('member_id');
            $table->unsignedBigInteger('reservation_id');
            $table->integer('flag')->default(0);
            $table->text('memo')->nullable();
            $table->timestamps();
        });

        // equipmentsテーブル
        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            $table->integer('active')->default(1);
            $table->string('cate_id');
            $table->string('title');
            $table->text('text1')->nullable();
            $table->text('text2')->nullable();
            $table->text('text3')->nullable();
            $table->text('text4')->nullable();
            $table->text('text5')->nullable();
            $table->integer('flag1')->default(0);
            $table->integer('flag2')->default(0);
            $table->integer('flag3')->default(0);
            $table->integer('flag4')->default(0);
            $table->integer('flag5')->default(0);
            $table->string('link')->nullable();
            $table->string('filename')->nullable();
            $table->integer('sort')->default(0);
            $table->timestamps();
        });

        // equipment_catesテーブル
        Schema::create('equipment_cates', function (Blueprint $table) {
            $table->id();
            $table->integer('active')->default(1);
            $table->string('title');
            $table->text('text')->nullable();
            $table->integer('flag')->default(0);
            $table->string('link')->nullable();
            $table->integer('sort')->default(0);
            $table->timestamps();
        });

        // schedulesテーブル
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->integer('item_id');
            $table->date('date');
            $table->time('time_start');
            $table->time('time_end');
            $table->integer('ts_start');
            $table->integer('ts_end');
            $table->time('request_time_start')->nullable();
            $table->time('request_time_end')->nullable();
            $table->integer('request_ts_start')->nullable();
            $table->integer('request_ts_end')->nullable();
            $table->string('status')->default('0');
            $table->integer('sort');
            $table->integer('flag')->default(0);
            $table->timestamps();
        });

        // reservationsテーブル
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->integer('member_id')->default(0);
            $table->string('email')->nullable();
            $table->string('tel')->nullable();
            $table->integer('kind')->default(0);
            $table->string('item_name')->nullable();
            $table->integer('item_id')->nullable();
            $table->unsignedBigInteger('equipment_id')->nullable();
            $table->date('date')->nullable();
            $table->timestamp('reserved_at')->nullable();
            $table->time('time_start')->nullable();
            $table->time('time_end')->nullable();
            $table->integer('course_id')->nullable();
            $table->integer('course_time')->nullable();
            $table->string('option')->nullable();
            $table->string('option_pay')->nullable();
            $table->string('costume')->nullable();
            $table->string('myoption')->nullable();
            $table->string('mycostume')->nullable();
            $table->text('comment')->nullable()->comment('備考');
            $table->text('memo')->nullable()->comment('スタッフ間メモ');
            $table->text('memo_share')->nullable()->comment('キャスト共有メモ');
            $table->text('memo_result')->nullable();
            $table->integer('fee_course')->default(0);
            $table->integer('fee_option')->default(0);
            $table->integer('fee_myoption')->nullable();
            $table->integer('fee_mycostume')->nullable();
            $table->integer('fee_adjust')->default(0);
            $table->text('fee_adjust_memo')->nullable();
            $table->integer('fee_total')->default(0);
            $table->text('area')->nullable()->comment('大まかなエリア');
            $table->text('place')->nullable()->comment('場所');
            $table->text('meet')->nullable()->comment('待ち合わせ方法');
            $table->text('address')->nullable()->comment('住所');
            $table->integer('flag')->default(0)->comment('0:未 1:確認 2:確定 3:保留');
            $table->integer('staff_id')->nullable();
            $table->timestamps();
        });
        // その後、データを更新
        DB::statement('UPDATE reservations SET reserved_at = CONCAT(date, " ", time_start) WHERE date IS NOT NULL AND time_start IS NOT NULL');

        // costumesテーブル
        Schema::create('costumes', function (Blueprint $table) {
            $table->id();
            $table->integer('active')->default(1);
            $table->string('name');
            $table->text('body')->nullable();
            $table->integer('cate')->default(0)->comment('0:未設定 1:私服 2:水着 3:その他');
            $table->integer('sort')->default(0);
            $table->string('filename')->nullable();
            $table->timestamps();
        });

        // costume_catesテーブル
        Schema::create('costume_cates', function (Blueprint $table) {
            $table->id();
            $table->integer('active')->default(1);
            $table->string('title');
            $table->text('text')->nullable();
            $table->integer('flag')->default(0);
            $table->integer('price')->nullable();
            $table->integer('sort')->default(0);
            $table->timestamps();
        });

        // galleriesテーブル
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->integer('active')->default(0);
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('member_id')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('sort')->default('0');
            $table->integer('flag')->default(0);
            $table->timestamps();
        });

        // gallery_imagesテーブル
        Schema::create('gallery_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('gallery_id');
            $table->string('filename');
            $table->string('caption')->nullable();
            $table->integer('sort')->default(0);
            $table->timestamps();
        });

        // custom_tableテーブル
        Schema::create('custom_table', function (Blueprint $table) {
            $table->id();
            $table->integer('cast_id');
            $table->date('date');
            $table->time('time_start');
            $table->time('time_end');
            $table->integer('ts_start');
            $table->integer('ts_end');
            $table->integer('sort');
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // items に依存するテーブルを先に削除
        Schema::dropIfExists('item_rank');
        Schema::dropIfExists('item_profile');
        Schema::dropIfExists('item_option_pay');
        Schema::dropIfExists('item_option');
        Schema::dropIfExists('item_icon_item');
        Schema::dropIfExists('mycostumes');
        Schema::dropIfExists('myoptions');
        Schema::dropIfExists('news');
        Schema::dropIfExists('pickups');

        // そのあと items を削除
        Schema::dropIfExists('items');

        // その他のテーブル（itemsに依存していないもの）は適宜ここで削除
        Schema::dropIfExists('users');
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('option_cates');
        Schema::dropIfExists('option_pays');
        Schema::dropIfExists('options');
        Schema::dropIfExists('icon_items');
        Schema::dropIfExists('icon_casts');
        Schema::dropIfExists('rank_cates');
        Schema::dropIfExists('ranks');
        Schema::dropIfExists('shops');
        Schema::dropIfExists('links');
        Schema::dropIfExists('mycostume_cates');
        Schema::dropIfExists('myoption_cates');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
    }
};
