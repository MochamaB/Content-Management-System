<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUtilitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('utilities', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('chartofaccounts_id')->index();
            $table->string('utility_name');
            $table->string('utility_type');
            $table->decimal('default_rate');
            $table->string('default_charge_cycle');
            $table->string('default_charge_type');
            $table->boolean('is_recurring_by_default')->default(true);

            $table->timestamps();
            $table->softDeletes();
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('chartofaccounts_id')->references('id')->on('chartofaccounts')->onDelete('cascade');
            // Ensure unique utility names per property
            $table->unique(['property_id', 'utility_name']);
        });
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('utilities');
    }
}
