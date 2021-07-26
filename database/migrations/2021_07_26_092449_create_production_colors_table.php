<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionColorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_colors', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->comment('色系');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE production_colors comment '作品色系'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_colors');
    }
}
