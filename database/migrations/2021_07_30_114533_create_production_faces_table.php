<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionFacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_faces', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->comment('脸型');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE production_styles comment '作品脸型'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_faces');
    }
}
