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
        Schema::create('tod_consumption_data', function (Blueprint $table) {
            $table->id();
            $table->integer('profile_id')->nullable();
            $table->integer('client_id')->nullable();
            $table->string('name')->nullable();
            $table->string('tb_1')->nullable();
            $table->string('tb_2')->nullable();
            $table->string('tb_3')->nullable();
            $table->string('tb_4')->nullable();
            $table->string('tb_5')->nullable();
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
        Schema::dropIfExists('tod_consumption_data');
    }
};
