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
        Schema::create('deposits', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('unit_id')->nullable()->index();
            $table->unsignedBigInteger('chartofaccount_id')->index();
            $table->string('model_type')->nullable(); // Who the deposit is for E.g., "User", "Vendor"
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('referenceno');
            $table->string('name'); //Name of the charge E.g Deposit,Utility,Maintenance
            $table->decimal('totalamount')->nullable();
            $table->string('status');
            $table->timestamp("duedate")->nullable();

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
        Schema::dropIfExists('deposits');
    }
};
