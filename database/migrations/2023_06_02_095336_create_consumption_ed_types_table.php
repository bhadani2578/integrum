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
        Schema::create('consumption_ed_types', function (Blueprint $table) {
            $table->id();
            $table->integer('profile_id')->nullable();
            $table->string('ed_type')->nullable()->default(1)->comment('1: waived, 2: rebate', '3:no rebate');
            $table->integer('waiver_time')->nullable()->default(1)->comment('1: available_upto, 2: month_waiver', '3:year_waiver');
            $table->string('available_upto')->nullable();
            $table->string('waiver_month')->nullable();
            $table->string('waiver_year')->nullable();
            $table->string('rebate_type')->nullable()->default(1)->comment('1: per unit, 2: percentage');
            $table->decimal('rebate_value', 8, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consumption_ed_types');
    }
};
