<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGenderAndAgeIdAndLengthIdAndColorIdStyleIdToProductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->integer('gender')->nullable()->after('order')->comment('性别 0男1女');
            $table->integer('age_id')->nullable()->after('gender')->comment('年龄段');
            $table->integer('length_id')->nullable()->after('age_id')->comment('长度');
            $table->integer('color_id')->nullable()->after('length_id')->comment('色系');
            $table->string('style_id')->nullable()->after('color_id')->comment('风格');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('productions', function (Blueprint $table) {
            //
        });
    }
}
