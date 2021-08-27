<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSortToReserveInformationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reserve_information', function (Blueprint $table) {
            $table->integer('sort')->default(0)->nullable()->comment('排序 数字越大越靠前');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reserve_information', function (Blueprint $table) {
            //
        });
    }
}
