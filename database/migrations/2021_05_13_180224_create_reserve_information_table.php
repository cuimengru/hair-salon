<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReserveInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reserve_information', function (Blueprint $table) {
            $table->id();
            $table->integer('designer_id')->comment('所属设计师ID');
            $table->string('service_project')->nullable()->comment('服务项目');
            $table->string('time')->nullable()->comment('预约时间');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE reserve_information comment '预约信息内容管理'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reserve_information');
    }
}
