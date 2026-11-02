<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        // shops
        Schema::create('shops', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('shop_name');
            $table->string('shop_active')->default('1');
            $table->string('shop_idname')->nullable();
            $table->string('shop_sub_name')->nullable();
            $table->string('shop_copy')->nullable();
            $table->string('shop_area')->nullable();
            $table->string('shop_tel')->nullable();
            $table->string('shop_tel2')->nullable();
            $table->string('shop_open')->nullable();
            $table->string('shop_zip1')->nullable();
            $table->string('shop_zip2')->nullable();
            $table->string('shop_pres')->nullable();
            $table->string('shop_address')->nullable();
            $table->string('shop_address2')->nullable();
            $table->string('shop_mail')->nullable();
            $table->string('shop_mail2')->nullable();
            $table->string('shop_domain')->nullable();
            $table->string('shop_url')->nullable();
            $table->string('shop_line')->nullable();
            $table->string('shop_line_id')->nullable();
            $table->string('shop_analytics')->nullable();
            $table->string('shop_payment')->nullable();
            $table->string('shop_reservation')->nullable();
            $table->string('shop_contact')->nullable();
            $table->string('shop_chat')->nullable();
            $table->string('shop_recruit')->nullable();
            $table->string('shop_recruit_mens')->nullable();
            $table->string('shop_x')->nullable();
            $table->string('shop_instagram')->nullable();
            $table->string('shop_youtube')->nullable();
            $table->string('shop_tiktok')->nullable();
            $table->string('shop_facebook')->nullable();
            $table->text('shop_map')->nullable();
            $table->string('shop_shame')->nullable();
            $table->string('shop_review')->nullable();
            $table->string('shop_sokuhime')->nullable();
            $table->string('shop_movie')->nullable();
            $table->string('shop_link1')->nullable();
            $table->string('shop_link2')->nullable();
            $table->string('shop_link3')->nullable();
            $table->string('shop_link4')->nullable();
            $table->text('shop_wiget1')->nullable();
            $table->text('shop_wiget2')->nullable();
            $table->text('shop_wiget3')->nullable();
            $table->timestamps();
        });

        // デフォルトshopレコードを追加
        DB::table('shops')->insert([
            'shop_name' => 'ショップ',
            'shop_active' => 1,
            'shop_idname' => 'shop',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // icon_casts
        Schema::create('icon_casts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('filename');
            $table->timestamps();
        });

        // 1. 親テーブル群
        Schema::create('icon_items', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->string('title');
            $table->string('filename')->nullable();
            $table->integer('sort')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->primary('id');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
        Schema::create('options', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->integer('cate')->default(0);
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('filename')->nullable();
            $table->integer('sort')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->primary('id');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
        Schema::create('option_pays', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->integer('cate')->default(0);
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('filename')->nullable();
            $table->string('price')->nullable();
            $table->integer('sort')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->primary('id');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
        Schema::create('profiles', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->string('title');
            $table->integer('sort')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->primary('id');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
        Schema::create('ranks', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->integer('active')->default(0);
            $table->unsignedBigInteger('cate_id')->nullable();
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('filename')->nullable()->comment('タイトル画像用xxx.jpg');
            $table->integer('sort')->default(0);
            $table->integer('position')->default(0);
            $table->date('term_start')->nullable();
            $table->date('term_end')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->primary('id');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
        Schema::create('rank_cates', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->string('title');
            $table->text('body')->nullable();
            $table->integer('sort')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->primary('id');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
        Schema::create('mycostume_cates', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->string('name');
            $table->text('body')->nullable();
            $table->integer('sort')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->primary('id');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
        Schema::create('myoption_cates', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->string('name');
            $table->text('body')->nullable();
            $table->integer('sort')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->primary('id');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
        // 2. 子テーブル群
        Schema::create('item_icon_item', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('item_icon_id');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->primary('id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('item_icon_id')->references('id')->on('icon_items')->onDelete('cascade');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
        Schema::create('item_option', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('option_id');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->primary('id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('option_id')->references('id')->on('options')->onDelete('cascade');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
        Schema::create('item_option_pay', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('option_pay_id');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->primary('id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('option_pay_id')->references('id')->on('option_pays')->onDelete('cascade');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
        Schema::create('item_profile', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('profile_id');
            $table->text('content')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->primary('id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
        Schema::create('item_rank', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('rank_id');
            $table->integer('no')->default(0);
            $table->text('text')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->primary('id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('rank_id')->references('id')->on('ranks')->onDelete('cascade');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
        Schema::create('mycostumes', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('mycostume_cate_id');
            $table->string('name');
            $table->integer('active')->default(0);
            $table->string('filename')->nullable();
            $table->text('body')->nullable();
            $table->integer('price')->default(0);
            $table->integer('sort')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->primary('id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('mycostume_cate_id')->references('id')->on('mycostume_cates')->onDelete('cascade');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
        Schema::create('myoptions', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('myoption_cate_id');
            $table->string('name');
            $table->integer('active')->default(0);
            $table->string('filename')->nullable();
            $table->text('body')->nullable();
            $table->integer('price')->default(0);
            $table->integer('sort')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->primary('id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('myoption_cate_id')->references('id')->on('myoption_cates')->onDelete('cascade');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // links
        Schema::create('links', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->string('title');
            $table->string('active')->default('1');
            $table->text('fullcode')->nullable();
            $table->string('link')->nullable();
            $table->integer('sort')->default(0);
            $table->string('filename')->nullable();
            $table->integer('position')->default(1)->comment('1:468x60 2:200x40 3:88x31 4:text 5:upper 6:bottom');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->primary('id');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // member_registrations
        Schema::create('member_registrations', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('member_id');
            $table->string('token');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->primary('id');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // newsテーブル
        Schema::create('news', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->integer('active')->default(0);
            $table->string('title');
            $table->integer('cate')->default(0);
            $table->text('body')->nullable();
            $table->string('link')->nullable();
            $table->string('filename')->nullable();
            $table->integer('timer_flag')->default(0);
            $table->dateTime('date_start')->nullable();
            $table->dateTime('date_end')->nullable();
            $table->integer('sort')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->primary('id');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // option_cates
        Schema::create('option_cates', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->integer('cate')->default(0);
            $table->string('title');
            $table->text('body')->nullable();
            $table->integer('sort')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->primary('id');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // pickups
        Schema::create('pickups', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->autoIncrement();
            $table->integer('item_id');
            $table->integer('sort');
            $table->dateTime('date');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            $table->primary('id');
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        // sessions
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // cache
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key', 255)->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        // cache_locks
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key', 255)->primary();
            $table->string('owner', 255);
            $table->integer('expiration');
        });

        // users
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // 子テーブル（外部キーでranksを参照）を先に削除
        Schema::dropIfExists('item_rank');

        // その後に親テーブル（ranks）を削除
        Schema::dropIfExists('ranks');
        Schema::dropIfExists('rank_cates');

        // 以下、他のテーブルも依存関係を意識して順に
        Schema::dropIfExists('myoptions');
        Schema::dropIfExists('myoption_cates');

        Schema::dropIfExists('mycostumes');
        Schema::dropIfExists('mycostume_cates');

        Schema::dropIfExists('item_profile');
        Schema::dropIfExists('profiles');

        Schema::dropIfExists('item_option_pay');
        Schema::dropIfExists('option_pays');

        Schema::dropIfExists('item_option');
        Schema::dropIfExists('options');

        Schema::dropIfExists('item_icon_item');
        Schema::dropIfExists('icon_items');

        Schema::dropIfExists('icon_casts');

        Schema::dropIfExists('news');
        Schema::dropIfExists('member_registrations');
        Schema::dropIfExists('links');
        Schema::dropIfExists('pickups');

        Schema::dropIfExists('shops');
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
    }
};
