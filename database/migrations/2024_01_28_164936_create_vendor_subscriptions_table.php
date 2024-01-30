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
        Schema::create('vendor_subscriptions', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('vendor_id')->index();
            $table->string('cycle');
            $table->decimal('price');
            $table->enum('subscription_status', ['Active', 'Not Paid','Suspended', 'No subscription', 'Complaint raised'])->default('No subscription');
            $table->timestamp("start_date")->nullable();
            $table->timestamp("end-date")->nullable();
 
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
           
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendor_subscriptions');
    }
};
