<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionStylesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_styles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->comment('风格');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE production_styles comment '作品风格'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_styles');
    }
}
