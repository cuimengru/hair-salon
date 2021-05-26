<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->nullable()->comment('评价类型 1设计师2商品');
            $table->integer('user_id')->comment('用户id');
            $table->integer('order_id')->nullable()->comment('订单id');
            $table->integer('reserveorder_id')->nullable()->comment('预约订单id');
            $table->integer('designer_id')->nullable()->comment('所属设计师ID');
            $table->integer('product_id')->nullable()->comment('对应商品ID');
            $table->integer('product_sku_id')->nullable()->comment('对应商品 SKU ID');
            $table->integer('rate')->default(0)->comment('评分');
            $table->string('render_content')->nullable()->comment('评论内容');
            $table->string('render_image')->nullable()->comment('评论图片');
            $table->string('render_video')->nullable()->comment('评论视频');
            $table->integer('status')->default(0)->comment('审核状态：0未审核1已审核');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE comments comment '评价管理'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
