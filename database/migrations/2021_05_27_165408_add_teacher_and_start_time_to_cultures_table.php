<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTeacherAndStartTimeToCulturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cultures', function (Blueprint $table) {
            $table->string('teacher')->nullable()->after('title')->comment('讲师');
            $table->dateTime('start_time')->nullable()->comment('开始时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cultures', function (Blueprint $table) {
            $table->dropColumn('teacher');
            $table->dropColumn('start_time');
        });
    }
}
