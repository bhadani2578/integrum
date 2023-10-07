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
        Schema::table('locking_periods', function (Blueprint $table) {
            $table->integer('lockin_period_month')->after('locking_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('locking_periods', function (Blueprint $table) {
            $table->dropColumn('lockin_period_month');
        });
    }
};
