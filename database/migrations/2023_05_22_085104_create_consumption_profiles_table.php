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
        Schema::create('consumption_profiles', function (Blueprint $table) {
            $table->id();            
            $table->string('point_name')->nullable();
            $table->integer('client_id')->nullable();
            $table->integer('state_id')->nullable();
            $table->integer('voltage_id')->nullable();
            $table->integer('discom_id')->nullable();
            $table->decimal('wheeling_charge', 8, 2)->nullable();
            $table->integer('discom_category_id')->nullable();
            $table->decimal('contract_demand', 8, 2)->nullable();
            $table->decimal('contract_demand_limitation', 8, 2)->nullable();                                                
            $table->integer('category_consumption_id')->nullable();
            $table->integer('granularity_level_id')->nullable();
            $table->string('consumption_file_path')->nullable();
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
        Schema::dropIfExists('consumption_profiles');
    }
};
