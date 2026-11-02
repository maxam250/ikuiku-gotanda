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
        // ============================================
        // 基本テーブル（依存関係なし）
        // ============================================

        // adminsテーブル
        if (!Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->string('email');
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('login_name')->unique();
                $table->string('role')->default('admin'); // integerからstringに変更済み
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
                'role' => 'super_admin',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // shopsテーブル
        if (!Schema::hasTable('shops')) {
            Schema::create('shops', function (Blueprint $table) {
                $table->id();
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
                'shop_active' => '1',
                'shop_idname' => 'shop',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // membersテーブル
        if (!Schema::hasTable('members')) {
            Schema::create('members', function (Blueprint $table) {
                $table->id();
                $table->integer('point')->default(0); // 追加済み
                $table->text('name'); // stringからtextに変更済み
                $table->text('email'); // stringからtextに変更済み
                $table->string('email_verified_at')->nullable();
                $table->string('loginid');
                $table->string('password');
                $table->date('birth')->nullable();
                $table->integer('gender')->default(0)->comment('0:未設定 1:男性 2:女性 3:その他');
                $table->integer('kind')->default(1)->comment('会員種別 0:停止 1:通常 2:プレミア');
                $table->string('check')->default('0')->comment('認証 0:未認証 1:認証済み');
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

            // membersテーブルのemailとloginidにunique制約を追加（text型のemailにはprefixインデックス）
            DB::statement('ALTER TABLE members ADD UNIQUE members_email_unique (email(191));');
            Schema::table('members', function (Blueprint $table) {
                $table->unique('loginid');
            });
        }

        // member_pointsテーブル
        if (!Schema::hasTable('member_points')) {
            Schema::create('member_points', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('member_id');
                $table->integer('point')->default(0);
                $table->enum('status', ['add', 'del']);
                $table->text('memo')->nullable();
                $table->foreign('member_id')->references('id')->on('members')->onDelete('cascade');
                $table->timestamps();
            });
        }

        // member_registrationsテーブル
        if (!Schema::hasTable('member_registrations')) {
            Schema::create('member_registrations', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('loginid')->unique();
                $table->string('email');
                $table->string('password');
                $table->timestamps();
            });
        }

        // member_password_reset_tokensテーブル
        if (!Schema::hasTable('member_password_reset_tokens')) {
            Schema::create('member_password_reset_tokens', function (Blueprint $table) {
                $table->string('email')->primary();
                $table->string('token');
                $table->timestamp('created_at')->nullable();
            });
        }

        // itemsテーブル
        if (!Schema::hasTable('items')) {
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
                $table->time('default_sch_start')->nullable(); // 追加済み
                $table->time('default_sch_end')->nullable(); // 追加済み
                $table->string('item_twitter')->nullable();
                $table->string('item_instagram')->nullable();
                $table->string('item_tiktok')->nullable();
                $table->string('item_youtube')->nullable();
                $table->integer('item_sort')->nullable();
                $table->string('item_line')->nullable();
                $table->string('item_tel')->nullable();
                $table->text('email')->nullable(); // stringからtextに変更済み
                $table->timestamp('email_verified_at')->nullable();
                $table->string('loginid')->nullable();
                $table->string('password')->nullable();
                $table->string('remember_token', 100)->nullable();
                $table->text('item_p_comment')->nullable();
                $table->dateTime('item_p_comment_date')->nullable();
                $table->timestamps();
            });
        }

        // item_imagesテーブル
        if (!Schema::hasTable('item_images')) {
            Schema::create('item_images', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('item_id');
                $table->string('filename');
                $table->integer('flag')->default(0);
                $table->integer('sort')->default(0);
                $table->timestamps();
            });
        }

        // ============================================
        // マスターテーブル（親テーブル）
        // ============================================

        // coursesテーブル
        if (!Schema::hasTable('courses')) {
            Schema::create('courses', function (Blueprint $table) {
                $table->id();
                $table->integer('active')->default(1);
                $table->string('title');
                $table->text('body')->nullable();
                $table->integer('sort')->default(0);
                $table->timestamps();
            });
        }

        // course_feesテーブル
        if (!Schema::hasTable('course_fees')) {
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
        }

        // eventsテーブル
        if (!Schema::hasTable('events')) {
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
        }

        // equipmentsテーブル
        if (!Schema::hasTable('equipments')) {
            Schema::create('equipments', function (Blueprint $table) {
                $table->id();
                $table->string('external_id')->nullable()->comment('外部DB用のID'); // 追加済み
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
                $table->string('filename_other')->nullable()->comment('見取り図用画像'); // 追加済み
                $table->string('map_url')->nullable()->comment('アクセスマップのリンクURL'); // 追加済み
                $table->text('map_code')->nullable()->comment('アクセスマップ（GoogleMap）のhtmlタグ'); // 追加済み
                $table->integer('sort')->default(0);
                $table->timestamps();
            });
        }

        // equipment_catesテーブル
        if (!Schema::hasTable('equipment_cates')) {
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
        }

        // equipment関連画像テーブル
        // ※ rename_studio_sub_images_to_equipment_sub_images で改名済みの場合もスキップ
        if (!Schema::hasTable('studio_sub_images') && !Schema::hasTable('equipment_sub_images')) {
            Schema::create('studio_sub_images', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('equipment_id');
                $table->string('filename');
                $table->string('name');
                $table->text('description')->nullable();
                $table->integer('sort')->default(0);
                $table->timestamps();
                $table->foreign('equipment_id', 'studio_sub_images_equip_fk')
                      ->references('id')->on('equipments')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('equipment_list_images')) {
            Schema::create('equipment_list_images', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('equipment_id');
                $table->string('filename');
                $table->string('name');
                $table->text('description')->nullable();
                $table->integer('sort')->default(0);
                $table->timestamps();
                $table->foreign('equipment_id')->references('id')->on('equipments')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('accessory_list_images')) {
            Schema::create('accessory_list_images', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('equipment_id');
                $table->string('filename');
                $table->string('name');
                $table->text('description')->nullable();
                $table->integer('sort')->default(0);
                $table->timestamps();
                $table->foreign('equipment_id')->references('id')->on('equipments')->onDelete('cascade');
            });
        }

        // costumesテーブル
        if (!Schema::hasTable('costumes')) {
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
        }

        // costume_catesテーブル
        if (!Schema::hasTable('costume_cates')) {
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
        }

        // icon_castsテーブル
        if (!Schema::hasTable('icon_casts')) {
            Schema::create('icon_casts', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('filename');
                $table->timestamps();
            });
        }

        // icon_itemsテーブル
        if (!Schema::hasTable('icon_items')) {
            Schema::create('icon_items', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('filename')->nullable();
                $table->integer('sort')->default(0);
                $table->timestamps();
            });
        }

        // optionsテーブル
        if (!Schema::hasTable('options')) {
            Schema::create('options', function (Blueprint $table) {
                $table->id();
                $table->integer('cate')->default(0);
                $table->string('title');
                $table->text('body')->nullable();
                $table->string('filename')->nullable();
                $table->integer('sort')->default(0);
                $table->timestamps();
            });
        }

        // option_paysテーブル
        if (!Schema::hasTable('option_pays')) {
            Schema::create('option_pays', function (Blueprint $table) {
                $table->id();
                $table->integer('cate')->default(0);
                $table->string('title');
                $table->text('body')->nullable();
                $table->string('filename')->nullable();
                $table->string('price')->nullable();
                $table->integer('sort')->default(0);
                $table->timestamps();
            });
        }

        // option_catesテーブル
        if (!Schema::hasTable('option_cates')) {
            Schema::create('option_cates', function (Blueprint $table) {
                $table->id();
                $table->integer('cate')->default(0);
                $table->string('title');
                $table->text('body')->nullable();
                $table->integer('sort')->default(0);
                $table->timestamps();
            });
        }

        // profilesテーブル
        if (!Schema::hasTable('profiles')) {
            Schema::create('profiles', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->integer('sort')->default(0);
                $table->timestamps();
            });
        }

        // ranksテーブル
        if (!Schema::hasTable('ranks')) {
            Schema::create('ranks', function (Blueprint $table) {
                $table->id();
                $table->integer('active')->default(0);
                $table->unsignedBigInteger('cate_id')->nullable();
                $table->string('title');
                $table->text('body')->nullable();
                $table->string('filename')->nullable()->comment('タイトル画像用xxx.jpg');
                $table->integer('sort')->default(0);
                $table->integer('position')->default(0);
                $table->date('term_start')->nullable();
                $table->date('term_end')->nullable();
                $table->timestamps();
            });
        }

        // rank_catesテーブル
        if (!Schema::hasTable('rank_cates')) {
            Schema::create('rank_cates', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('body')->nullable();
                $table->integer('sort')->default(0);
                $table->timestamps();
            });
        }

        // mycostume_catesテーブル
        if (!Schema::hasTable('mycostume_cates')) {
            Schema::create('mycostume_cates', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('body')->nullable();
                $table->integer('sort')->default(0);
                $table->timestamps();
            });
        }

        // myoption_catesテーブル
        if (!Schema::hasTable('myoption_cates')) {
            Schema::create('myoption_cates', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('body')->nullable();
                $table->integer('sort')->default(0);
                $table->timestamps();
            });
        }

        // linksテーブル
        if (!Schema::hasTable('links')) {
            Schema::create('links', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('active')->default('1');
                $table->text('fullcode')->nullable();
                $table->string('link')->nullable();
                $table->integer('sort')->default(0);
                $table->string('filename')->nullable();
                $table->integer('position')->default(1)->comment('1:468x60 2:200x40 3:88x31 4:text 5:upper 6:bottom');
                $table->timestamps();
            });
        }

        // newsテーブル
        if (!Schema::hasTable('news')) {
            Schema::create('news', function (Blueprint $table) {
                $table->id();
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
                $table->timestamps();
            });
        }

        // pickupsテーブル
        if (!Schema::hasTable('pickups')) {
            Schema::create('pickups', function (Blueprint $table) {
                $table->id();
                $table->integer('item_id');
                $table->integer('sort');
                $table->dateTime('date');
                $table->timestamps();
            });
        }

        // interviewsテーブル
        if (!Schema::hasTable('interviews')) {
            Schema::create('interviews', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->integer('sort')->default(0);
                $table->timestamps();
            });
        }

        // main_visualsテーブル
        if (!Schema::hasTable('main_visuals')) {
            Schema::create('main_visuals', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('pc_image_path')->nullable();
                $table->string('mobile_image_path')->nullable();
                $table->string('link_url')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('show_on_pc')->default(true);
                $table->boolean('show_on_mobile')->default(true);
                $table->timestamp('start_date')->nullable();
                $table->timestamp('end_date')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // blogsテーブル
        if (!Schema::hasTable('blogs')) {
            Schema::create('blogs', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('content');
                $table->string('main_image')->nullable();
                $table->json('sub_images')->nullable();
                $table->boolean('is_published')->default(false);
                $table->timestamps();
            });
        }

        // couponsテーブル
        if (!Schema::hasTable('coupons')) {
            Schema::create('coupons', function (Blueprint $table) {
                $table->id();
                $table->string('code')->nullable()->unique();
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('main_image')->nullable();
                $table->boolean('is_active')->default(true);
                $table->date('expires_at')->nullable();
                $table->timestamps();
            });
        }

        // special_eventsテーブル
        if (!Schema::hasTable('special_events')) {
            Schema::create('special_events', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->date('event_date');
                $table->integer('start_hour');
                $table->integer('start_minute');
                $table->integer('end_hour');
                $table->integer('end_minute');
                $table->text('description');
                $table->json('item_ids')->nullable();
                $table->string('main_image')->nullable();
                $table->integer('fee')->nullable();
                $table->string('fee_note')->nullable();
                $table->string('venue')->nullable();
                $table->text('access_info')->nullable();
                $table->string('map_url')->nullable();
                $table->boolean('is_active')->default(false);
                $table->string('external_link')->nullable();
                $table->text('notes')->nullable();
                $table->boolean('has_timer')->default(false);
                $table->date('timer_start')->nullable();
                $table->date('timer_end')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // ============================================
        // 中間テーブル（items関連）
        // ============================================

        // item_icon_itemテーブル
        if (!Schema::hasTable('item_icon_item')) {
            Schema::create('item_icon_item', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('item_id');
                $table->unsignedBigInteger('item_icon_id');
                $table->timestamps();
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
                $table->foreign('item_icon_id')->references('id')->on('icon_items')->onDelete('cascade');
            });
        }

        // item_optionテーブル
        if (!Schema::hasTable('item_option')) {
            Schema::create('item_option', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('item_id');
                $table->unsignedBigInteger('option_id');
                $table->timestamps();
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
                $table->foreign('option_id')->references('id')->on('options')->onDelete('cascade');
            });
        }

        // item_option_payテーブル
        if (!Schema::hasTable('item_option_pay')) {
            Schema::create('item_option_pay', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('item_id');
                $table->unsignedBigInteger('option_pay_id');
                $table->timestamps();
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
                $table->foreign('option_pay_id')->references('id')->on('option_pays')->onDelete('cascade');
            });
        }

        // item_profileテーブル
        if (!Schema::hasTable('item_profile')) {
            Schema::create('item_profile', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('item_id');
                $table->unsignedBigInteger('profile_id');
                $table->text('content')->nullable();
                $table->timestamps();
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
                $table->foreign('profile_id')->references('id')->on('profiles')->onDelete('cascade');
            });
        }

        // item_rankテーブル
        if (!Schema::hasTable('item_rank')) {
            Schema::create('item_rank', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('item_id')->nullable();
                $table->unsignedBigInteger('rank_id');
                $table->integer('no')->default(0);
                $table->text('text')->nullable();
                $table->timestamps();
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
                $table->foreign('rank_id')->references('id')->on('ranks')->onDelete('cascade');
            });
        }

        // mycostumesテーブル
        if (!Schema::hasTable('mycostumes')) {
            Schema::create('mycostumes', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('item_id');
                $table->unsignedBigInteger('mycostume_cate_id');
                $table->string('name');
                $table->integer('active')->default(0);
                $table->string('filename')->nullable();
                $table->text('body')->nullable();
                $table->integer('price')->default(0);
                $table->integer('sort')->default(0);
                $table->timestamps();
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
                $table->foreign('mycostume_cate_id')->references('id')->on('mycostume_cates')->onDelete('cascade');
            });
        }

        // myoptionsテーブル
        if (!Schema::hasTable('myoptions')) {
            Schema::create('myoptions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('item_id');
                $table->unsignedBigInteger('myoption_cate_id');
                $table->string('name');
                $table->integer('active')->default(0);
                $table->string('filename')->nullable();
                $table->text('body')->nullable();
                $table->integer('price')->default(0);
                $table->integer('sort')->default(0);
                $table->timestamps();
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
                $table->foreign('myoption_cate_id')->references('id')->on('myoption_cates')->onDelete('cascade');
            });
        }

        // item_courseテーブル
        if (!Schema::hasTable('item_course')) {
            Schema::create('item_course', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('item_id');
                $table->unsignedBigInteger('course_id');
                $table->enum('status', ['ok', 'ng', 'confirm'])->default('ng');
                $table->timestamps();
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
                $table->unique(['item_id', 'course_id']);
            });
        }

        // item_course_feesテーブル
        if (!Schema::hasTable('item_course_fees')) {
            Schema::create('item_course_fees', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('item_id');
                $table->unsignedBigInteger('course_id');
                $table->integer('fee_60')->nullable();
                $table->integer('fee_90')->nullable();
                $table->integer('fee_120')->nullable();
                $table->integer('fee_150')->nullable();
                $table->integer('fee_180')->nullable();
                $table->integer('fee_extension')->nullable();
                $table->integer('uniform_rate')->nullable();
                $table->boolean('use_uniform_rate')->default(false);
                $table->timestamps();
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
                $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
                $table->unique(['item_id', 'course_id']);
            });
        }

        // item_interviewテーブル
        if (!Schema::hasTable('item_interview')) {
            Schema::create('item_interview', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('item_id');
                $table->unsignedBigInteger('interview_id');
                $table->text('content')->nullable();
                $table->timestamps();
                $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
                $table->foreign('interview_id')->references('id')->on('interviews')->onDelete('cascade');
            });
        }

        // items_special_eventsテーブル
        if (!Schema::hasTable('items_special_events')) {
            Schema::create('items_special_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('special_event_id')->constrained()->onDelete('cascade');
                $table->foreignId('item_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });
        }

        // coupon_itemsテーブル
        if (!Schema::hasTable('coupon_items')) {
            Schema::create('coupon_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('coupon_id')->constrained()->onDelete('cascade');
                $table->foreignId('item_id')->constrained()->onDelete('cascade');
                $table->json('coupon_data')->nullable();
                $table->timestamps();
                $table->unique(['coupon_id', 'item_id']);
            });
        }

        // ============================================
        // 予約・スケジュール関連テーブル
        // ============================================

        // schedulesテーブル
        if (!Schema::hasTable('schedules')) {
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
        }

        // reservationsテーブル
        if (!Schema::hasTable('reservations')) {
            Schema::create('reservations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('shop_id')->default(3); // 追加済み
                $table->string('name')->nullable();
                $table->integer('member_id')->default(0);
                $table->text('email')->nullable(); // stringからtextに変更済み
                $table->text('tel')->nullable(); // stringからtextに変更済み
                $table->integer('kind')->default(0);
                $table->string('item_name')->nullable();
                $table->string('item_name_other')->nullable(); // 追加済み
                $table->integer('item_id')->nullable();
                $table->unsignedBigInteger('equipment_id')->nullable();
                $table->date('date')->nullable();
                $table->timestamp('reserved_at')->nullable(); // 追加済み
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
                // admin-projectから追加
                $table->enum('gender', ['male', 'female', 'noanswer'])->nullable();
                $table->enum('p_category', ['studio', 'outgoing', 'considering'])->nullable();
                $table->string('p_plan')->nullable();
                $table->string('p_option')->nullable();
                $table->text('background')->nullable();
                $table->string('make_plan')->nullable();
                $table->tinyInteger('make_flag')->nullable();
                $table->enum('make_status', ['none', 'confirming', 'confirmed', 'pending'])->nullable();
                $table->unsignedBigInteger('outsourcing_id')->nullable();
                $table->string('p_outsourcing')->nullable();
                $table->enum('payment', ['square', 'paypay_bank', 'other_bank', 'invoice', 'prepaid', 'cash', 'other'])->nullable();
                $table->tinyInteger('payment_flag')->nullable();
                $table->enum('place_status', ['studio', 'studio_area', 'outgoing'])->nullable();
                $table->string('useage')->nullable();
                $table->enum('publish', ['ng', 'gallery', 'all'])->nullable();
                $table->text('reference1')->nullable();
                $table->text('reference2')->nullable();
                $table->string('filename1')->nullable();
                $table->string('filename2')->nullable();
                $table->string('corporate_name')->nullable();
                $table->string('color_code', 7)->nullable();
                $table->tinyInteger('estimated_duration')->nullable();
                $table->tinyInteger('guest_count')->nullable();
                $table->integer('status')->nullable();
                $table->timestamps();
            });

            // reserved_atカラムのデータを更新
            DB::statement('UPDATE reservations SET reserved_at = CONCAT(date, " ", time_start) WHERE date IS NOT NULL AND time_start IS NOT NULL');
        }

        // staffsテーブル
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

            // staffsテーブルの外部キー制約
            Schema::table('staffs', function (Blueprint $table) {
                $table->foreign('default_studio_id', 'staffs_default_studio_id_foreign')
                    ->references('id')
                    ->on('equipments')
                    ->onDelete('set null');
            });
        }

        // outsourcingsテーブル
        if (!Schema::hasTable('outsourcings')) {
            Schema::create('outsourcings', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->string('phone')->nullable();
                $table->text('contact_info')->nullable();
                $table->decimal('commission_rate', 5, 2)->default(0);
                $table->text('invoice_info')->nullable();
                $table->text('memo')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamp('email_verified_at')->nullable();
                $table->integer('sort')->default(0);
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // historiesテーブル
        if (!Schema::hasTable('histories')) {
            Schema::create('histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('member_id');
                $table->unsignedBigInteger('reservation_id');
                $table->integer('flag')->default(0);
                $table->text('memo')->nullable();
                $table->timestamps();
            });
        }

        // ============================================
        // ギャラリー関連テーブル
        // ============================================

        // galleriesテーブル
        if (!Schema::hasTable('galleries')) {
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
        }

        // gallery_imagesテーブル
        if (!Schema::hasTable('gallery_images')) {
            Schema::create('gallery_images', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('gallery_id');
                $table->string('filename');
                $table->string('caption')->nullable();
                $table->integer('sort')->default(0);
                $table->timestamps();
            });
        }

        // ============================================
        // メッセージ関連テーブル
        // ============================================

        // messagesテーブル
        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->unsignedBigInteger('sender_id');
                $table->string('sender_type'); // 'admin' or 'member'
                $table->unsignedBigInteger('receiver_id');
                $table->string('receiver_type'); // 'admin' or 'member'
                $table->text('message');
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->string('image_path')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // message_templatesテーブル
        if (!Schema::hasTable('message_templates')) {
            Schema::create('message_templates', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('content');
                $table->text('description')->nullable();
                $table->enum('type', ['member', 'cast', 'common'])->default('common');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // message_itemsテーブル
        if (!Schema::hasTable('message_items')) {
            Schema::create('message_items', function (Blueprint $table) {
                $table->id();
                $table->string('title')->nullable();
                $table->unsignedBigInteger('sender_id');
                $table->string('sender_type'); // 'admin' or 'item'
                $table->unsignedBigInteger('receiver_id');
                $table->string('receiver_type'); // 'admin' or 'item'
                $table->text('message');
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->string('image_path')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // guest_messagesテーブル
        if (!Schema::hasTable('guest_messages')) {
            Schema::create('guest_messages', function (Blueprint $table) {
                $table->id();
                $table->text('email')->comment('暗号化されたメールアドレス');
                $table->string('sender_type')->comment('admin or guest');
                $table->text('message');
                $table->string('title')->nullable();
                $table->string('image_path')->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->unsignedBigInteger('reservation_id')->nullable()->comment('関連する予約ID');
                $table->string('mailgun_message_id')->nullable()->comment('Mailgunメッセージ ID');
                $table->timestamps();
                $table->softDeletes();
                $table->index('reservation_id');
                $table->index('created_at');
                $table->index('mailgun_message_id');
            });

            // emailカラムのインデックスを手動で追加
            DB::statement('ALTER TABLE guest_messages ADD INDEX idx_email_prefix (email(100))');
        }

        // ============================================
        // その他テーブル
        // ============================================

        // contactsテーブル
        if (!Schema::hasTable('contacts')) {
            Schema::create('contacts', function (Blueprint $table) {
                $table->id();
                $table->string('name')->comment('お名前');
                $table->text('email')->comment('メールアドレス');
                $table->text('phone')->nullable()->comment('電話番号');
                $table->enum('inquiry_type', ['general', 'reservation', 'cast', 'other'])->comment('問い合わせ種別');
                $table->string('subject')->comment('件名');
                $table->text('message')->comment('問い合わせ内容');
                $table->enum('status', ['unread', 'in_progress', 'completed'])->default('unread')->comment('ステータス');
                $table->text('admin_memo')->nullable()->comment('管理者メモ');
                $table->string('ip_address')->nullable()->comment('IPアドレス（スパム対策）');
                $table->string('user_agent')->nullable()->comment('ユーザーエージェント（スパム対策）');
                $table->boolean('is_spam')->default(false)->comment('スパムフラグ');
                $table->timestamps();
            });
        }

        // admin_notificationsテーブル
        if (!Schema::hasTable('admin_notifications')) {
            Schema::create('admin_notifications', function (Blueprint $table) {
                $table->id();
                $table->string('type')->comment('member_message, item_message, attendance_request, new_reservation');
                $table->unsignedBigInteger('related_id')->comment('関連するレコードのID');
                $table->string('title');
                $table->text('message');
                $table->string('sender');
                $table->string('color')->comment('blue, purple, orange, green');
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();
            });
        }

        // custom_tableテーブル
        if (!Schema::hasTable('custom_table')) {
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

        // usersテーブル（Laravel標準）
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // sessionsテーブル（Laravel標準）
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

        // cacheテーブル（Laravel標準）
        if (!Schema::hasTable('cache')) {
            Schema::create('cache', function (Blueprint $table) {
                $table->string('key', 255)->primary();
                $table->mediumText('value');
                $table->integer('expiration');
            });
        }

        // cache_locksテーブル（Laravel標準）
        if (!Schema::hasTable('cache_locks')) {
            Schema::create('cache_locks', function (Blueprint $table) {
                $table->string('key', 255)->primary();
                $table->string('owner', 255);
                $table->integer('expiration');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 依存関係を考慮して逆順で削除

        // 中間テーブルを先に削除
        Schema::dropIfExists('coupon_items');
        Schema::dropIfExists('items_special_events');
        Schema::dropIfExists('item_interview');
        Schema::dropIfExists('item_course_fees');
        Schema::dropIfExists('item_course');
        Schema::dropIfExists('myoptions');
        Schema::dropIfExists('mycostumes');
        Schema::dropIfExists('item_rank');
        Schema::dropIfExists('item_profile');
        Schema::dropIfExists('item_option_pay');
        Schema::dropIfExists('item_option');
        Schema::dropIfExists('item_icon_item');
        Schema::dropIfExists('accessory_list_images');
        Schema::dropIfExists('equipment_list_images');
        Schema::dropIfExists('studio_sub_images');
        Schema::dropIfExists('gallery_images');
        Schema::dropIfExists('galleries');
        Schema::dropIfExists('guest_messages');
        Schema::dropIfExists('message_items');
        Schema::dropIfExists('message_templates');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('admin_notifications');
        Schema::dropIfExists('contacts');
        Schema::dropIfExists('histories');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('item_images');
        Schema::dropIfExists('items');
        Schema::dropIfExists('member_password_reset_tokens');
        Schema::dropIfExists('member_registrations');
        Schema::dropIfExists('member_points');
        Schema::dropIfExists('members');
        Schema::dropIfExists('pickups');
        Schema::dropIfExists('news');
        Schema::dropIfExists('special_events');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('blogs');
        Schema::dropIfExists('main_visuals');
        Schema::dropIfExists('interviews');
        Schema::dropIfExists('links');
        Schema::dropIfExists('myoption_cates');
        Schema::dropIfExists('mycostume_cates');
        Schema::dropIfExists('rank_cates');
        Schema::dropIfExists('ranks');
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('option_cates');
        Schema::dropIfExists('option_pays');
        Schema::dropIfExists('options');
        Schema::dropIfExists('icon_items');
        Schema::dropIfExists('icon_casts');
        Schema::dropIfExists('costume_cates');
        Schema::dropIfExists('costumes');
        Schema::dropIfExists('equipment_cates');
        Schema::dropIfExists('equipments');
        Schema::dropIfExists('events');
        Schema::dropIfExists('course_fees');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('custom_table');
        Schema::dropIfExists('shops');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
    }
};
