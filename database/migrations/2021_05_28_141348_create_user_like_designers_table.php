<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLikeDesignersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_like_designers', function (Blueprint $table) {
            $table->id();
            $table->integer('type')->nullable()->comment('浏览类型:1商品2转售3发型师4作品');
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('product_id')->nullable()->comment('集品商品id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unsignedBigInteger('selfproduct_id')->nullable()->comment('自营商品id');
            $table->foreign('selfproduct_id')->references('id')->on('products')->onDelete('cascade');
            $table->unsignedBigInteger('idleproduct_id')->nullable()->comment('转售商品id');
            $table->foreign('idleproduct_id')->references('id')->on('products')->onDelete('cascade');
            $table->unsignedBigInteger('designer_id')->nullable()->comment('设计师id');
            $table->foreign('designer_id')->references('id')->on('designers')->onDelete('cascade');
            $table->unsignedBigInteger('production_id')->nullable()->comment('作品id');
            $table->foreign('production_id')->references('id')->on('productions')->onDelete('cascade');
            $table->integer('count')->default('1')->comment('浏览次数');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE user_like_designers comment '浏览记录管理'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_like_designers');
    }
}
