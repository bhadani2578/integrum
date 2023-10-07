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
        Schema::table('mapping_priority', function (Blueprint $table) {
            DB::statement('ALTER TABLE mapping_priority CHANGE `mapping` `digit` INTEGER');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mapping_priority', function (Blueprint $table) {
            $table->renameColumn('`digit`', '`mapping`');
        });
    }
};