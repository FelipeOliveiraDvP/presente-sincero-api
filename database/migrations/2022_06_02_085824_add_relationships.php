<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationships extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reset_password', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('role')->references('id')->on('roles');
        });

        Schema::table('contests', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('contests', function (Blueprint $table) {
            $table->foreign('winner_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->foreign('contest_id')->references('id')->on('contests')->onDelete('cascade');
        });

        Schema::table('gallery', function (Blueprint $table) {
            $table->foreign('contest_id')->references('id')->on('contests')->onDelete('cascade');
        });

        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::table('contest_bank', function (Blueprint $table) {
            $table->foreign('bank_id')->references('id')->on('bank_accounts')->onDelete('cascade');
        });

        Schema::table('contest_bank', function (Blueprint $table) {
            $table->foreign('contest_id')->references('id')->on('contests')->onDelete('cascade');
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
