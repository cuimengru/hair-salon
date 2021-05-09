<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductSkusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_skus', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('SKU名称');
            $table->string('description')->comment('SKU描述');
            $table->decimal('price', 10, 2)->comment('SKU价格');
            $table->unsignedInteger('stock')->comment('库存');
            $table->unsignedBigInteger('product_id')->comment('所属商品id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE product_skus comment '商品的SKU表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_skus');
    }
}
