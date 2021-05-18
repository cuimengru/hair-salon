<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLabelIdAndManyImageAndPropertyAndPackageMailToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('label_id')->nullable()->after('country')->comment('所属标签id');
            $table->string('many_image')->nullable()->after('image')->comment('多图');
            $table->json('property')->nullable()->after('original_price')->comment('商品属性');
            $table->integer('package_mail')->nullable()->after('property')->comment('是否包邮 0否1是');
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
            $table->dropColumn('label_id');
            $table->dropColumn('many_image');
            $table->dropColumn('property');
            $table->dropColumn('package_mail');
        });
    }
}
