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
        Schema::table('annual_source_profiles', function (Blueprint $table) {
            $table->renameColumn('annual_year', 'year_unit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('annual_source_profiles', function (Blueprint $table) {
            $table->renameColumn('annual_year', 'year_unit');
        });
    }
};
