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
        Schema::create('source_profiles', function (Blueprint $table) {
            $table->id();
            $table->integer('client_id');
            $table->string('source_name');
            $table->integer('type_source_id')->nullable();
            $table->integer('type_arrangement_id')->nullable();
            $table->integer('type_contract_id')->nullable();
            $table->integer('banking_arragement_id')->nullable();
            $table->integer('settlement_id');
            $table->integer('state_id')->nullable();
            $table->integer('discoms_id')->nullable();
            $table->string('voltage_connectivity');
            $table->integer('annual_traffic_type')->nullable()->default(1)->comment('1: Percentage, 2: Float/unit');
            $table->integer('annual_traffic_value')->nullable();
            $table->string('quantum')->nullable();
            $table->decimal('minimum_off_take',8,2)->nullable();
            $table->string('application_period')->nullable();
            $table->integer('lockin_period')->nullable();
            $table->string('source_profile_path')->nullable();
            $table->integer('granularity_id')->nullable();
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
        Schema::dropIfExists('source_profiles');
    }
};
