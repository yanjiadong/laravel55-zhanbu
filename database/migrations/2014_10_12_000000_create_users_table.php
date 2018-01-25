<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('wechat_avatar')->default('')->comment('微信头像')->nullable();
            $table->string('wechat_nickname')->default('')->comment('微信昵称');
            $table->string('wechat_openid')->default('')->comment('微信openid');
            $table->text('wechat_original')->comment('微信返回的信息  json储存')->nullable();
            $table->tinyInteger('sex')->default(1)->comment('1=男 2=女');
            $table->string('birth_year',12)->comment('出生年');
            $table->string('birth_month',12)->default('')->comment('出生月');
            $table->string('birth_day',12)->default('')->comment('出生日');
            $table->string('birth_hour',12)->default('')->comment('出生时');
            $table->string('birth_minute',12)->default('')->comment('出生分');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
