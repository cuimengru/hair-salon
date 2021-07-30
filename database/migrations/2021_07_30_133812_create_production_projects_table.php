<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_projects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->comment('项目');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE production_styles comment '作品属性项目'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_projects');
    }
}
