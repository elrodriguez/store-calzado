<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('local_sales', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->string('address');
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        DB::table('local_sales')->insert([
            ['description' => 'Local Principal', 'address' => 'Viru', 'phone' => '99999999']
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('local_sales');
    }
};
