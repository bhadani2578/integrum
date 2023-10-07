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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id')->nullable();
            $table->string('site_name')->nullable();
            $table->string('project_location')->nullable();
            $table->integer('mapping_id')->nullable();
            $table->integer('clipping')->nullable();
            $table->integer('clipping_value')->nullable();
            $table->integer('total_capacity')->nullable();
            $table->integer('percentage_satisfied_value')->nullable();
            $table->integer('grid_power')->nullable();
            $table->integer('green_energy_power')->nullable();
            $table->integer('evacuation_capacity')->nullable();
            $table->integer('lapsed_unit')->nullable();
            $table->integer('connected_voltage')->nullable();
            $table->integer('connected_voltage_value')->nullable();
            $table->integer('wind_capex')->nullable();
            $table->integer('solar_capex')->nullable();
            $table->integer('total_capex')->nullable();
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
        Schema::dropIfExists('projects');
    }
};
