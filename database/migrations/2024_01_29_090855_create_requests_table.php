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
        Schema::create('requests', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('unit_id')->nullable()->index();
            $table->unsignedBigInteger('chartofaccount_id')->index();
            $table->string('subject');
            $table->string('category');
            $table->string('desc');
            $table->string('status');
            $table->string('raised_by');
            $table->string('assigned_type'); // Either user or vendor //////
            $table->unsignedBigInteger('assigned_id');

            $table->timestamps();
            $table->softDeletes();
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('chartofaccount_id')->references('id')->on('chartofaccounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requests');
    }
};
