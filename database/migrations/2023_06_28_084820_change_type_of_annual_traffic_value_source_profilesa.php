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
            DB::statement('ALTER TABLE source_profiles MODIFY COLUMN annual_traffic_value VARCHAR(255)');
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
            //
        });
    }
};
