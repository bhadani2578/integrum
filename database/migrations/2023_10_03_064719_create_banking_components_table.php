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
        Schema::create('banking_components', function (Blueprint $table) {
            $table->id();
            $table->integer('project_id')->nullable();
            $table->integer('client_id')->nullable();
            $table->string('banking_basis')->nullable();
            $table->string('banking_charges')->nullable();                     
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
        Schema::dropIfExists('banking_components');
    }
};
