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
            $table->integer('locking_period_month_id')->after('locking_period_id')->null();
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
            $table->dropColumn('locking_period_month_id');
        });
    }
};
