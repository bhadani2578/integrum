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
        Schema::table('tod_state_consumption_data', function (Blueprint $table) {        
            $table->string('consumed_unit')->nullable()->after('dec');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tod_state_consumption_data', function (Blueprint $table) {
            //
        });
    }
};
