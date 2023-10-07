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
            if (Schema::hasColumns('source_profiles', ['lockin_period', 'application_period', 'voltage_connectivity']))
            {
                $table->dropColumn('lockin_period');
                $table->dropColumn('application_period');
                $table->dropColumn('voltage_connectivity');
            }
            $table->integer('locking_period_id');
            $table->integer('applicable_period_id');
            $table->integer('voltage_id');
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
            $table->dropColumn('locking_period_id');
            $table->dropColumn('applicable_period_id');
            $table->dropColumn('voltage_id');
        });
    }
};
