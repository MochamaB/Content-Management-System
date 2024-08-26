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
        Schema::create('payments', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('unit_id')->nullable()->index();
            $table->string('model_type')->nullable(); // What payment is for - Invoices,Deposit,Maintenance,Subscription
            $table->unsignedBigInteger('model_id')->nullable(); // ID of the specific model instance
            $table->string('referenceno')->nullable();
            $table->unsignedBigInteger('payment_method_id');
 	        $table->string('payment_code')->nullable();
            $table->decimal('totalamount')->nullable();
            $table->decimal('taxamount')->nullable();
            $table->enum('status', ['allocated', 'unallocated',])->nullable();
            $table->string('received_by')->nullable();
            $table->string('reviewed_by')->nullable();
            $table->timestamp("invoicedate")->nullable();
 
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('cascade');
            $table->foreign('payment_method_id')->references('id')->on('payment_methods')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
