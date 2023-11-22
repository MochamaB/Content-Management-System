<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('unit_id')->index();
            $table->string('charge_name'); ///charge Name,
            $table->unsignedBigInteger('transactionable_id');   ////id value of the model
            $table->string('transactionable_type'); ///Name of the Model, Invoice,Voucher, Fees,
            $table->string('description');
            $table->unsignedBigInteger('debitaccount_id');
            $table->unsignedBigInteger('creditaccount_id');
            $table->decimal('amount');
            $table->timestamps();
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('debitaccount_id')->references('id')->on('chartofaccounts');
            $table->foreign('creditaccount_id')->references('id')->on('chartofaccounts');
        });
            
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
