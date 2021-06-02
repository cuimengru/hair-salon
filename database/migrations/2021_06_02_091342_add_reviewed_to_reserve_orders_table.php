<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReviewedToReserveOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reserve_orders', function (Blueprint $table) {
            $table->boolean('reviewed')->default(false)->after('status')->comment('订单是否已评价');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reserve_orders', function (Blueprint $table) {
            $table->dropColumn('reviewed');
        });
    }
}
