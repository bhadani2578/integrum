<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * add start_date and end_date in source_profiles table
     */
    public function up()
    {
        Schema::table('source_profiles', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('annual_traffic_value');
            $table->date('end_date')->nullable()->after('start_date');
        });
    }

    /**
     * drop column
     */
    public function down()
    {
        Schema::table('source_profiles', function (Blueprint $table) {
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
        });
    }
};
