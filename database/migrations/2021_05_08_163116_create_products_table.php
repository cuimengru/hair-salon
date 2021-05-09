<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id')->nullable()->comment('类目');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->string('title')->comment('商品名称');
            $table->text('description')->comment('商品详情');
            $table->string('image')->comment('封面图片');
            $table->boolean('on_sale')->default(true)->comment('商品是否正在售卖，default 1');
            $table->float('rating')->default(5)->comment('商品平均评分');
            $table->unsignedInteger('sold_count')->default(0)->comment('销量');
            $table->unsignedInteger('review_count')->default(0)->comment('评价数量');
            $table->decimal('price', 10, 2)->comment('SKU最低价格');
            $table->decimal('original_price', 10, 2)->comment('原价');
            $table->integer('type')->comment('商品类型 1集品 2自营 3闲置');
            $table->integer('is_recommend')->default(0)->comment('是否推荐 0否1是');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE products comment '商品信息表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
