<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVipCodingAndVipBalanceAndViporiginalBalanceToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('vip_coding')->nullable()->comment('贵宾卡编码');
            $table->decimal('vip_balance', 10, 2)->nullable()->comment('贵宾卡余额');
            $table->decimal('viporiginal_balance', 10, 2)->nullable()->comment('贵宾卡原始余额');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
