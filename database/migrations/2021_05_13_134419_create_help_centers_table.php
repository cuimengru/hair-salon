<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHelpCentersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('help_centers', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('标题');
            $table->string('description')->nullable()->comment('描述');
            $table->string('content')->nullable()->comment('内容');
            $table->integer('is_recommend')->default(0)->comment('是否推荐 0否1是');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE help_centers comment '帮助信息管理'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('help_centers');
    }
}
