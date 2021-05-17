<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServiceProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_projects', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('服务项目名称');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE service_projects comment '服务项目管理'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('service_projects');
    }
}
