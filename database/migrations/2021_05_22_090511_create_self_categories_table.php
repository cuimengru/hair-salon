<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSelfCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('self_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('类目名称');
            $table->unsignedBigInteger('parent_id')->nullable()->comment('父类目ID');
            $table->boolean('is_directory')->nullable()->comment('是否拥有子类目');
            $table->unsignedInteger('level')->nullable()->comment('level');
            $table->string('path')->nullable()->comment('该类目所有父类目 id');
            $table->integer('order')->default(0)->after('path')->comment('排序');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE self_categories comment '自营商品类目表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('self_categories');
    }
}
