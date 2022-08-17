<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFailedOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('failed_orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('order_id')->unsigned();
            $table->bigInteger('contest_id')->unsigned();
            $table->bigInteger('customer_id')->unsigned();
            $table->longText('cause');
            $table->enum('current_order_status', ['PROCESSING', 'PENDING', 'CANCELED', 'CONFIRMED']);
            $table->enum('next_order_status', ['PROCESSING', 'PENDING', 'CANCELED', 'CONFIRMED']);
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
        Schema::dropIfExists('failed_orders');
    }
}
