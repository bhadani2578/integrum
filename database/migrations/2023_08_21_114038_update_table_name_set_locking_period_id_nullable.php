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
        DB::statement('ALTER TABLE source_profiles MODIFY locking_period_id INT NULL');
        DB::statement('ALTER TABLE source_profiles MODIFY locking_period_month_id INT NULL');
        DB::statement('ALTER TABLE source_profiles MODIFY applicable_period_id INT NULL');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
