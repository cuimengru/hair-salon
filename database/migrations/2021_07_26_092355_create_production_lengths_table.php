<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionLengthsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('production_lengths', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable()->comment('长度');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE production_lengths comment '作品长度'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('production_lengths');
    }
}
