<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCommentToAdvertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //hyh新增广告类型 增加修改数据表备注信息
        Schema::table('adverts', function (Blueprint $table) {
            $table->integer('type')->comment('类型 0填写内容 1跳转到站内产品 2跳转到外部广告 3只有图片')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('adverts', function (Blueprint $table) {
            //
        });
    }
}
