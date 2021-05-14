<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('adverts', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable()->comment('标题');
            $table->string('description')->nullable()->comment('描述');
            $table->string('thumb')->nullable()->comment('图片');
            $table->string('url')->nullable()->comment('跳转链接');
            $table->integer('order')->default(0)->comment('排序');
            $table->integer('is_recommend')->default(0)->comment('是否推荐 0否1是');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE adverts comment '广告管理'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('adverts');
    }
}
