<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNicknameAndPhoneAndIntroduceAndIntegralAndBalanceAndStatusToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nickname')->nullable()->comment('昵称');
            $table->string('phone')->nullable()->comment('手机号');
            $table->string('introduce')->nullable()->comment('简介');
            $table->decimal('integral',8,2)->default(0.00)->comment('积分');
            $table->decimal('balance',8,2)->default(0.00)->comment('余额');
            $table->integer('status')->default(0)->comment('审核状态:0未审核1已审核-1审核中');
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
            $table->dropColumn('nickname');
            $table->dropColumn('phone');
            $table->dropColumn('introduce');
            $table->dropColumn('integral');
            $table->dropColumn('balance');
            $table->dropColumn('status');
        });
    }
}
