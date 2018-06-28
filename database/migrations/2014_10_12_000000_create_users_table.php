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
            $table->string('name');
            $table->string('truename')->nullable();
            $table->tinyInteger('sex')->default('1');
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('password');
            $table->tinyInteger('identify')->default(5);
            $table->integer('school_id')->default(0)->comment('学校id');
            $table->integer('notification_count')->unsigned()->default(0)->comment('消息数');
            $table->tinyInteger('status')->default(1)->comment('状态');
            $table->string('openid')->unique()->nullable();
            $table->string('weixin_session_key')->nullable();
            $table->string('avatar')->nullable();
            $table->string('address')->nullable();
            $table->rememberToken();
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
