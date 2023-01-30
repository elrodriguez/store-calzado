<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('petty_cashes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->date('date_opening');
            $table->time('time_opening');
            $table->date('date_closed');
            $table->time('time_closed');
            $table->decimal('beginning_balance', 12, 2);
            $table->time('final_balance', 12, 2);
            $table->time('income', 12, 2);
            $table->char('state', 1);
            $table->string('reference_number', 20)->nullable();
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
        Schema::dropIfExists('petty_cashes');
    }
};
