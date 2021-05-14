<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDesignersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('designers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('姓名');
            $table->string('thumb')->comment('图片');
            $table->string('position')->nullable()->comment('职位');
            $table->integer('rating')->default(0)->comment('评价数量');
            $table->string('description')->nullable()->comment('描述');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE designers comment '设计师信息管理'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('designers');
    }
}
