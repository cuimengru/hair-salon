<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaidAtAndPaymentNoToReserveOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reserve_orders', function (Blueprint $table) {
            $table->dateTime('paid_at')->nullable()->after('payment_method')->comment('支付时间');
            $table->string('payment_no')->nullable()->after('paid_at')->comment('支付平台订单号');
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
            $table->dropColumn('paid_at');
            $table->dropColumn('payment_no');
        });
    }
}
