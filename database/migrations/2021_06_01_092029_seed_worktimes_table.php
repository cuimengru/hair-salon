<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SeedWorktimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $time = [
            [
                'time' => '8:30',
            ],
            [
                'time' => '9:00',
            ],
            [
                'time' => '9:30',
            ],
            [
                'time' => '10:00',
            ],
            [
                'time' => '10:30',
            ],
            [
                'time' => '11:00',
            ],
            [
                'time' => '11:30',
            ],
            [
                'time' => '12:00',
            ],
            [
                'time' => '12:30',
            ],
            [
                'time' => '13:00',
            ],
            [
                'time' => '13:30',
            ],
            [
                'time' => '14:00',
            ],
            [
                'time' => '14:30',
            ],
            [
                'time' => '15:00',
            ],
            [
                'time' => '15:30',
            ],[
                'time' => '16:00',
            ],
            [
                'time' => '16:30',
            ],[
                'time' => '17:00',
            ],[
                'time' => '17:30',
            ],[
                'time' => '18:00',
            ],[
                'time' => '18:30',
            ],[
                'time' => '19:00',
            ],[
                'time' => '19:30',
            ],[
                'time' => '20:00',
            ],[
                'time' => '20:30',
            ],
        ];

        DB::table('worktimes')->insert($time);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('worktimes')->truncate();
    }
}
