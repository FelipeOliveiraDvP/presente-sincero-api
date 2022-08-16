<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('contest_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->decimal('total', 5, 2)->unsigned();
            $table->json('numbers');
            $table->enum('status', ['PROCESSING', 'PENDING', 'CANCELED', 'CONFIRMED'])->default('PROCESSING');
            $table->string('transaction_code')->nullable();
            $table->date('confirmed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('contest_id')->references('id')->on('contests')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
