<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contests', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('winner_id')->unsigned();
            $table->string('title');
            $table->date('start_date');
            $table->date('contest_date')->nullable();
            $table->decimal('price', 5, 2)->unsigned();
            $table->integer('quantity')->unsigned();
            $table->string('short_description');
            $table->text('full_description');
            $table->string('whatsapp_number');
            $table->string('whatsapp_group')->nullable();
            $table->json('numbers');
            $table->enum('status', ['ACTIVE', 'FINISHED'])->default('ACTIVE');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contests');
    }
}
