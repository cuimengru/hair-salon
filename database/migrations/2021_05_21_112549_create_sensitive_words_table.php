<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSensitiveWordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sensitive_words', function (Blueprint $table) {
            $table->id();
            $table->string('word')->comment('敏感词');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE sensitive_words comment '敏感词'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sensitive_words');
    }
}
