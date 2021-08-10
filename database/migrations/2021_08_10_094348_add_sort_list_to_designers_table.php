<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSortListToDesignersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('designers', function (Blueprint $table) {
            $table->integer('sort_list')->default(0)->nullable()->comment('设计师排序 数字越大越靠前');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('designers', function (Blueprint $table) {
            //
        });
    }
}
