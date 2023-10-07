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
        Schema::create('loan_components', function (Blueprint $table) {
            $table->id();
            $table->integer('project_id')->nullable();
            $table->integer('client_id')->nullable();
            $table->string('gst')->nullable();
            $table->string('income_tax')->nullable();
            $table->string('cash_equity')->nullable();
            $table->string('debt')->nullable();
            $table->string('total_fund')->nullable();
            $table->string('rate_of_interest')->nullable();
            $table->string('repayment_period')->nullable();
            $table->string('moratorium')->nullable();
            $table->string('tax_rate')->nullable();
            $table->string('depreciation_rate')->nullable();
            $table->string('addl_depreciation_rate')->nullable();
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
        Schema::dropIfExists('loan_components');
    }
};
