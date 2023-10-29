<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitchargesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unitcharges', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('parent_utility')->nullable();
            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('unit_id')->index();
            $table->unsignedBigInteger('chartofaccounts_id')->index();
            $table->string('charge_name');
            $table->string('charge_cycle');
            $table->string('charge_type');
            $table->decimal('rate');
            $table->string('recurring_charge');
            $table->timestamp("startdate")->nullable();
            $table->timestamp("nextdate")->nullable();
            $table->timestamps();
            $table->foreign('parent_utility')->references('id')->on('unitcharges')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('chartofaccounts_id')->references('id')->on('chartofaccounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('unitcharges');
    }
}
