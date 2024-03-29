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
            $table->string('model_type'); // Who the voucher goes to E.g., "User", "Vendor"
            $table->unsignedBigInteger('model_id'); // ID of the specific model instance
            $table->string('referenceno');
            $table->string('voucher_type'); //Name of the charge E.g Rent,Utility,Maintenance
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
