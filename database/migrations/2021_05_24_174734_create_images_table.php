<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->index();
            $table->string('type')->index()->comment('类型');;
            $table->string('path')->comment('路径');
            $table->string('disk')->comment('磁盘名');
            $table->string('size')->comment('大小');
            $table->double('size_kb', 8, 2)->default(0)->comment('大小KB');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE images comment '图片资源'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
}
