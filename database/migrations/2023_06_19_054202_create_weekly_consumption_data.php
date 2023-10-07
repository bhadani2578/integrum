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
        Schema::create('weekly_consumption_data', function (Blueprint $table) {
            $table->id();
            $table->integer('profile_id')->nullable();
            $table->integer('client_id')->nullable();
            $table->string('weeks')->nullable();
            $table->string('consumed_unit')->nullable();
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
        Schema::dropIfExists('weekly_consumption_data');
    }
};
