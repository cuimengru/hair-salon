<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionAgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_ages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->comment('年龄段');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE production_ages comment '作品年龄段'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_ages');
    }
}
