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
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->string('short_name')->unique();
            $table->string('name')->unique();
            $table->string('description');
            $table->string('ruc')->unique();
            $table->string('telephone');
            $table->string('email')->unique();
            $table->string('image')->nullable();
            $table->string('address');
            $table->string('contact_telephone');
            $table->string('contact_name');
            $table->string('contact_email');
            $table->string('ubigeo');
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
        Schema::dropIfExists('providers');
    }
};
