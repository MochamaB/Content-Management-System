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
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('unit_id')->index();
            $table->unsignedBigInteger('chartofaccounts_id')->index();
            $table->unsignedBigInteger('utility_id')->nullable()->index();
            $table->string('charge_name');
            $table->string('charge_cycle');
            $table->string('charge_type');
            $table->decimal('rate');
            $table->string('recurring_charge');
            $table->timestamp("startdate")->nullable();
            $table->timestamp("nextdate")->nullable();
            $table->boolean('override_defaults')->default(false);
            // Optional: Add columns to track which fields are overridden
            $table->json('overridden_fields')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->foreign('parent_id')->references('id')->on('unitcharges')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('chartofaccounts_id')->references('id')->on('chartofaccounts')->onDelete('cascade');
            $table->foreign('utility_id')->references('id')->on('utilities')->onDelete('cascade');
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
