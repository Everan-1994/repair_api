<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order')->unique()->comment('订单号');
            $table->tinyInteger('type')->comment('申报类型');
            $table->tinyInteger('school_id')->comment('学校id');
            $table->tinyInteger('area_id')->comment('申报区域');
            $table->string('address')->comment('申报地址');
            $table->string('content')->comment('申报事项');
            $table->unsignedInteger('user_id')->index()->comment('申报者id');
            $table->integer('repair_id')->index()->default(0)->comment('维修员id');
            $table->tinyInteger('assess')->default(0)->comment('评价：0-未评价、1-好评、2-中评、3-差评');
            $table->string('assess_content')->nullable()->comment('评价内容');
            $table->tinyInteger('status')->default(0)->comment('工单状态：0-驳回、1-审核、2-派工、3-完工、4-申诉(调回第一步)');
            $table->string('form_id')->unique()->comment('发送模板消息的form_id');
            $table->string('repair_form_id')->unique()->comment('发送模板消息的form_id');
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
        Schema::dropIfExists('orders');
    }
}
