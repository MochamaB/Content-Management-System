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
        Schema::create('paymentvouchers', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('unit_id')->nullable()->index();
            $table->string('payable_type')->nullable();// What the voucher is for to E.g., "Unitcharge", "Expense"
            $table->unsignedBigInteger('payable_id')->nullable(); // ID of the specific model instance
            $table->string('model_type')->nullable(); // Who the voucher goes to E.g., "User", "Vendor"
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
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paymentvouchers');
    }
};
