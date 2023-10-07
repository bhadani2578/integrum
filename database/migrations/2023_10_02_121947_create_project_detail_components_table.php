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
        Schema::create('project_detail_components', function (Blueprint $table) {
            $table->id();
            $table->integer('project_id')->nullable();
            $table->integer('client_id')->nullable();
            $table->string('no_turbines')->nullable();
            $table->string('solar_mwp')->nullable();
            $table->string('dc_ac_ratio')->nullable();
            $table->string('solar_unit_mwp')->nullable();
            $table->string('solar_deration')->nullable();
            $table->string('wind_capacity_mws')->nullable();
            $table->string('wind_gen_unit_turbine')->nullable();
            $table->string('total_gen')->nullable();
            $table->string('solar_capex')->nullable();
            $table->string('wind_capex')->nullable();
            $table->string('total_capex')->nullable();
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
        Schema::dropIfExists('project_detail_components');
    }
};
