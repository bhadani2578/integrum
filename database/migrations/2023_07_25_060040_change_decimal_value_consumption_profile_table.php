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
        Schema::table('consumption_profiles', function (Blueprint $table) {
            DB::statement('ALTER TABLE consumption_profiles MODIFY COLUMN contract_demand_limitation DECIMAL(50, 2)');
            DB::statement('ALTER TABLE consumption_profiles MODIFY COLUMN wheeling_charge DECIMAL(50, 2)');
            DB::statement('ALTER TABLE consumption_profiles MODIFY COLUMN contract_demand DECIMAL(50, 2)');
            DB::statement('ALTER TABLE consumption_profiles MODIFY COLUMN rebate_value DECIMAL(50, 2)');
            // $table->decimal('contract_demand_limitation', 50, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consumption_profiles', function (Blueprint $table) {
            // $table->decimal('contract_demand_limitation', 8, 2);
        });
    }
};
