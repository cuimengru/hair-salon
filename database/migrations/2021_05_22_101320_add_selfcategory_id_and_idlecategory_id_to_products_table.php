<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSelfcategoryIdAndIdlecategoryIdToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('selfcategory_id')->nullable()->after('category_id')->comment('自营商品类目');
            $table->unsignedBigInteger('idlecategory_id')->nullable()->after('selfcategory_id')->comment('闲置商品类目');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('selfcategory_id');
            $table->dropColumn('idlecategory_id');
        });
    }
}
