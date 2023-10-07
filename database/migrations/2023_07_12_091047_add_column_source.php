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
        Schema::table('source_profiles', function (Blueprint $table)
        {
            $table->integer('supply_commitment')->nullable()->after('quantum');
            $table->decimal('minimum_supply',8,2)->nullable()->after('supply_commitment');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('source_profiles', function (Blueprint $table) {
            $table->dropColumn('supply_commitment');
            $table->dropColumn('minimum_supply');
        });
    }
};
