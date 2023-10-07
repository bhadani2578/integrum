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
        Schema::table('source_profiles', function (Blueprint $table) {
            $table->decimal('loan', 50, 2)->nullable()->default(null)->after('quantum');
            $table->decimal('annual_maintain', 50, 2)->nullable()->default(null)->after('loan');
            $table->decimal('insurance', 50, 2)->nullable()->default(null)->after('annual_maintain');
            $table->decimal('revenue_unit', 50, 2)->nullable()->default(null)->after('insurance');
            $table->decimal('depreciation_benefit', 50, 2)->nullable()->default(null)->after('revenue_unit');
            $table->decimal('transmission_charges', 50, 2)->nullable()->default(null)->after('depreciation_benefit');
            $table->decimal('wheeling_charges', 50, 2)->nullable()->default(null)->after('transmission_charges');
            $table->decimal('electricity_duty', 50, 2)->nullable()->default(null)->after('wheeling_charges');
            $table->decimal('asset_fees', 50, 2)->nullable()->default(null)->after('electricity_duty');
            $table->decimal('energy_landed_cost', 50, 2)->nullable()->default(null)->after('asset_fees');
            $table->decimal('statutory_charge', 50, 2)->nullable()->default(null)->after('energy_landed_cost');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
