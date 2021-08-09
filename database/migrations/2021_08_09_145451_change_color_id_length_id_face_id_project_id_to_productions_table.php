<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColorIdLengthIdFaceIdProjectIdToProductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        hyh单选改为多选
        Schema::table('productions', function (Blueprint $table) {
            $table->string('color_id')->nullable()->change();//发质
            $table->string('length_id')->nullable()->change();//长度
            $table->string('face_id')->nullable()->change();//脸型
            $table->string('project_id')->nullable()->change();//项目
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('productions', function (Blueprint $table) {
            //
        });
    }
}
