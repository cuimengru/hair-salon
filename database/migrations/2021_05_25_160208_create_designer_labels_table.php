<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDesignerLabelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('designer_labels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('名称');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE designer_labels comment '设计师标签管理'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('designer_labels');
    }
}
