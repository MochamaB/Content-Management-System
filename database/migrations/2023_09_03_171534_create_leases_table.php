<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leases', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('unit_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('lease_period');
            $table->string('status');
            $table->timestamp("startdate")->nullable();
            $table->timestamp("enddate")->nullable();
            $table->timestamps();
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leases');
    }
}
