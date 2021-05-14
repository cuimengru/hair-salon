<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReserveOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reserve_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('reserve_id')->comment('预约信息id');
            $table->string('user_id')->comment('用户id');
            $table->string('service_project')->nullable()->comment('服务项目');
            $table->string('time')->nullable()->comment('预约时间');
            $table->integer('num')->default(1)->comment('预约人数');
            $table->string('phone')->nullable()->comment('手机号');
            $table->string('remark')->nullable()->comment('备注');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE reserve_orders comment '预约订单管理'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reserve_orders');
    }
}
