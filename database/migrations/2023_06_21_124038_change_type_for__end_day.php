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
        Schema::table('consumption_day_shifts', function (Blueprint $table) {
            DB::statement('ALTER TABLE consumption_day_shifts MODIFY COLUMN day_end VARCHAR(255)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consumption_day_shifts', function (Blueprint $table) {
            //
        });
    }
};
