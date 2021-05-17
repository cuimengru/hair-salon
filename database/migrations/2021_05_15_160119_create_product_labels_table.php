<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_labels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名称');
            $table->integer('order')->default(0)->comment('排序');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE product_labels comment '产品标签管理'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_labels');
    }
}
