<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNumbersPercentageColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contests', function (Blueprint $table) {
            $table->boolean('show_percentage')->default(false)->after('max_reserve_days');
            $table->boolean('use_custom_percentage')->default(false)->after('show_percentage');
            $table->decimal('paid_percentage', 1, 1)->default(0)->after('use_custom_percentage');
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
