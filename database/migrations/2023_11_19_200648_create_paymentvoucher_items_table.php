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
        Schema::create('paymentvoucher_items', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('paymentvoucher_id')->index();
            $table->unsignedBigInteger('unitcharge_id')->nullable()->index();
            $table->unsignedBigInteger('chartofaccount_id')->index();
            $table->string('description')->nullable();
            $table->string('payment_method')->nullable();
            $table->decimal('amount');
            $table->timestamps();
            $table->foreign('paymentvoucher_id')->references('id')->on('paymentvouchers')->onDelete('cascade');
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
        Schema::dropIfExists('paymentvoucher_items');
    }
};
