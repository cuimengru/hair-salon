<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsNewAndIsNewLableToProductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        hyh作品 增加新品标识
        Schema::table('productions', function (Blueprint $table) {
            $table->integer('is_new')->default(0)->nullable()->comment('是否为新品 0为否 1为是');
            $table->string('is_new_lable')->nullable()->comment('自定义新品标识');
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
    }
}
