<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('no')->unique()->comment('订单号');
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->text('address')->comment('收货地址');
            $table->decimal('total_amount', 10, 2)->comment('订单总金额');
            $table->text('remark')->nullable()->comment('订单备注');
            $table->dateTime('paid_at')->nullable()->comment('支付时间');
            $table->string('payment_method')->nullable()->comment('支付方式');
            $table->string('payment_no')->nullable()->comment('支付平台订单号');
            $table->string('status')->comment('订单状态');
            $table->string('refund_no')->unique()->nullable()->comment('退款单号');
            $table->boolean('closed')->default(false)->comment('订单是否已关闭');
            $table->boolean('reviewed')->default(false)->comment('订单是否已评价');
            $table->string('ship_status')->comment('物流状态');
            $table->text('ship_data')->nullable()->comment('物流数据');
            $table->text('extra')->nullable()->comment('其他额外的数据');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE orders comment '订单'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
