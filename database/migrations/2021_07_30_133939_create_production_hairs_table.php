<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionHairsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_hairs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->comment('烫染');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE production_styles comment '作品属性烫染'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_hairs');
    }
}
