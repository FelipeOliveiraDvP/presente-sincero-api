<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNumbersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('numbers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('contest_id')->unsigned();
            $table->double('price', 5, 2, true)->default(0);
            $table->integer('start')->unsigned()->default(0);
            $table->integer('quantity')->unsigned()->default(1);
            $table->integer('per_customer')->unsigned()->default(1);
            $table->json('numbers'); // { number: "00000", status: FREE | RESERVED | PAID, customer_id: 123 | null }
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('numbers');
    }
}
