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
        Schema::create('mapping', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id')->nullable();
            $table->string('mapping_name');
            $table->integer('consumption_point_id');
            $table->integer('source_point_id');
            $table->integer('mapping_priority_id');
            $table->integer('granularity_id');
            $table->integer('quantum');
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
        Schema::dropIfExists('mapping');
    }
};
