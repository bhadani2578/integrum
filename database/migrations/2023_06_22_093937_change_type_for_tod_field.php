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
        Schema::table('consumption_tods', function (Blueprint $table) {
            DB::statement('ALTER TABLE consumption_tods MODIFY COLUMN tod_start VARCHAR(255)');
            DB::statement('ALTER TABLE consumption_tods MODIFY COLUMN tod_end VARCHAR(255)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consumption_tods', function (Blueprint $table) {
            //
        });
    }
};
