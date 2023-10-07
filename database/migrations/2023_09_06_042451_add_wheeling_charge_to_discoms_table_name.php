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
        Schema::table('discoms', function (Blueprint $table) {
            DB::statement('ALTER TABLE discoms ADD wheeling_charge DECIMAL(50, 2) DEFAULT 0 AFTER name');
    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('discoms', function (Blueprint $table) {
            //
        });
    }
};
