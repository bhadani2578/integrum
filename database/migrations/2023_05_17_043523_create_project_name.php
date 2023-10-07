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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('parent_group')->nullable();
            $table->string('person_name')->nullable();
            $table->string('designation')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->integer('lead_type')->default(0)->comment('0: direct, 1: indirect');
            $table->string('consultant_name')->nullable();
            $table->integer('commission_fee')->default(0)->comment('0: inr/unit, 1: inr/mw, 2: both');
            $table->integer('type_of_industry')->nullable();
            $table->integer('consumption_point_no')->nullable();
            $table->integer('source_point_no')->nullable();
            $table->integer('is_metadata')->default(0)->comment('0: not_upload, 1: metadata_upload');
            $table->integer('status')->default(0)->comment('0: Active, 1: Deactivated');
            $table->timestamps();
            $table->softDeletes(); // Add soft delete column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
};
