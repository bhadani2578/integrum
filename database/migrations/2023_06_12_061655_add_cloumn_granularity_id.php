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
            $table->integer('granularity_id')->after('granularity_level_id')->default('1');
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
            $table->dropColumn('granularity_id');
        });
    }
};
