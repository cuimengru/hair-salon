<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeavetimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leavetimes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('designer_id')->nullable()->comment('设计师id');
            $table->foreign('designer_id')->references('id')->on('designers')->onDelete('cascade');
            $table->integer('type')->default(0)->comment('请假类型: 0全天 1半天');
            $table->date('date')->nullable()->comment('日期');
            $table->json('time')->nullable()->comment('时间段');

            $table->timestamps();
        });
        DB::statement("ALTER TABLE leavetimes comment '设计师请假时间管理'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leavetimes');
    }
}
