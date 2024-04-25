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
        Schema::create('transaction_types', function (Blueprint $table) {
            $table->id()->index();
            $table->string('name');
            $table->string('description');
            $table->string('model');
            $table->string('account_type');
            $table->unsignedBigInteger('debitaccount_id')->nullable();
            $table->unsignedBigInteger('creditaccount_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('debitaccount_id')->references('id')->on('chartofaccounts')->onDelete('cascade');
            $table->foreign('creditaccount_id')->references('id')->on('chartofaccounts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_types');
    }
};
