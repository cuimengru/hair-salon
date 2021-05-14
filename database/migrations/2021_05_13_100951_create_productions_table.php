<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('标题');
            $table->string('thumb')->nullable()->comment('封面图片');
            $table->string('video')->nullable()->comment('视频');
            $table->string('description')->nullable()->comment('描述');
            $table->string('content')->nullable()->comment('内容');
            $table->integer('rating')->default(0)->comment('浏览次数');
            $table->integer('is_recommend')->default(0)->comment('是否推荐 0否1是');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE productions comment '作品管理'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productions');
    }
}
