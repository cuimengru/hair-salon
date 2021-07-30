<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHeightIdAndFaceIdAndProjectIdAndHairIdToProductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('productions', function (Blueprint $table) {
            $table->integer('height_id')->nullable()->after('style_id')->comment('身高id');
            $table->integer('face_id')->nullable()->after('height_id')->comment('脸型id');
            $table->integer('project_id')->nullable()->after('face_id')->comment('项目id');
            $table->integer('hair_id')->nullable()->after('project_id')->comment('烫染id');
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
