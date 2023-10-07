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
        Schema::create('annual_maintenance_components', function (Blueprint $table) {
            $table->id();
            $table->integer('project_id')->nullable();
            $table->integer('client_id')->nullable();
            $table->string('solar_maintenance')->nullable();
            $table->string('soalr_free')->nullable();
            $table->string('solar_escalation')->nullable();
            $table->string('wind_maintenance')->nullable();
            $table->string('wind_free')->nullable();
            $table->string('wind_escalation')->nullable();
            $table->string('bop_maintenance')->nullable();
            $table->string('bop_free')->nullable();
            $table->string('bop_escalation')->nullable();            
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
        Schema::dropIfExists('annual_maintenance_components');
    }
};
