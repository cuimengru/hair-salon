<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeCommentToProductionsAndDesigersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->integer('sort')->default(0)->nullable()->comment('作品推荐的排序 数字越大越靠前')->change();
        });

        Schema::table('designers', function (Blueprint $table) {
            $table->integer('sort')->default(0)->nullable()->comment('设计师推荐的排序 数字越大越靠前')->change();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('productions', function (Blueprint $table) {
            //
        });

        Schema::table('desigers', function (Blueprint $table) {
        //
        });

    }
}
