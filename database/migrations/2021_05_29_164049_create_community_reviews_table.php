<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommunityReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('community_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->comment('用户ID');
            $table->unsignedBigInteger('replyuser_id')->nullable()->comment('回复用户ID');
            $table->unsignedBigInteger('community_id')->nullable()->comment('社区id');
            $table->json('message')->comment('消息内容');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE community_reviews comment '社区评论内容管理'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('community_reviews');
    }
}
