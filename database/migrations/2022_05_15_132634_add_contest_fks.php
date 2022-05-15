<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContestFks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->foreign('contest_id')->references('id')->on('contests');
        });

        Schema::table('gallery', function (Blueprint $table) {
            $table->foreign('contest_id')->references('id')->on('contests');
        });

        Schema::table('rewards', function (Blueprint $table) {
            $table->foreign('contest_id')->references('id')->on('contests');
        });

        Schema::table('numbers', function (Blueprint $table) {
            $table->foreign('contest_id')->references('id')->on('contests');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
