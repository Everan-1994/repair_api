<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEvaluatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluates', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id')->comment('工单id');
            $table->unsignedInteger('ps_id')->comment('进度id');
            $table->string('evaluate')->comment('好评类型');
            $table->string('content')->comment('评价内容');
            $table->integer('service')->comment('服务星评');
            $table->integer('efficiency')->comment('效率星评');
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
        Schema::dropIfExists('evaluates');
    }
}
