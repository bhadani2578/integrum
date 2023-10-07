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
        Schema::create('consumption_day_shifts', function (Blueprint $table) {
            $table->id();
            $table->integer('profile_id')->nullable();
            $table->integer('day_start')->nullable()->default(1);
            $table->integer('day_end')->nullable()->default(1);
            $table->integer('shift_start')->nullable()->default(0);
            $table->integer('shift_end')->nullable()->default(0);
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
        Schema::dropIfExists('consumption_day_shifs');
    }
};
