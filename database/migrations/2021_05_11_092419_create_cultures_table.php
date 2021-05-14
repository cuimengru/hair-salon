<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCulturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cultures', function (Blueprint $table) {
            $table->id();
            $table->integer('place_id')->nullable()->comment('位置：1教育2培训3线下活动');
            $table->string('title')->nullable()->comment('标题');
            $table->string('description')->nullable()->comment('描述');
            $table->string('content')->nullable()->comment('内容');
            $table->string('thumb')->nullable()->comment('封面图片');
            $table->string('video')->nullable()->comment('视频');
            $table->string('video_url')->nullable()->comment('视频链接');
            $table->integer('is_recommend')->default('0')->comment('是否推荐 0否1是');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE cultures comment '文化中心'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cultures');
    }
}
