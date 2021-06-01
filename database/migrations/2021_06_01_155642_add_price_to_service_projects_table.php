<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceToServiceProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('service_projects', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->after('name')->comment('价格');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('service_projects', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
}
