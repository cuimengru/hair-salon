<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusAndPaymentMethodAndNoToReserveOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reserve_orders', function (Blueprint $table) {
            $table->string('no')->unique()->after('id')->comment('订单号');
            $table->string('payment_method')->nullable()->after('money')->comment('支付方式');
            $table->string('status')->after('payment_method')->comment('订单状态');
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
            $table->dropColumn('no');
            $table->dropColumn('payment_method');
            $table->dropColumn('status');
        });
    }
}
