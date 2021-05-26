<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManyImagesAndCertificateAndHonorAndScoreAndLabelIdToDesignersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('designers', function (Blueprint $table) {
            $table->string('many_images')->nullable()->after('thumb')->comment('多图');
            $table->json('certificate')->nullable()->after('description')->comment('证书');
            $table->json('honor')->nullable()->after('certificate')->comment('荣誉');
            $table->decimal('score',2,1)->default(0.0)->after('honor')->comment('评分');
            $table->string('label_id')->nullable()->after('score')->comment('所属设计师标签ID');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('designers', function (Blueprint $table) {
            $table->dropColumn('many_images');
            $table->dropColumn('certificate');
            $table->dropColumn('honor');
            $table->dropColumn('score');
            $table->dropColumn('label_id');
        });
    }
}
