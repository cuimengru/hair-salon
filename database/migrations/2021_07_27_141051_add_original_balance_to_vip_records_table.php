<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOriginalBalanceToVipRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vip_records', function (Blueprint $table) {
            $table->decimal('original_balance',10, 2)->default(0.00)->comment('充值前的余额');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vip_records', function (Blueprint $table) {
            //
        });
    }
}
