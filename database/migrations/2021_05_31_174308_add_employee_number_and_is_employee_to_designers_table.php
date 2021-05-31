<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmployeeNumberAndIsEmployeeToDesignersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('designers', function (Blueprint $table) {
            $table->string('employee_number')->nullable()->after('is_recommend')->comment('员工号');
            $table->integer('is_employee')->default(0)->after('employee_number')->comment('是否是员工 0否 1是');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('designers', function (Blueprint $table) {
            $table->dropColumn('employee_number');
            $table->dropColumn('is_employee');
        });
    }
}
