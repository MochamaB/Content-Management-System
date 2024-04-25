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
        Schema::create('expense_items', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('expense_id')->index();
            $table->unsignedBigInteger('unitcharge_id')->nullable()->index();
            $table->unsignedBigInteger('chartofaccount_id')->index();
            $table->string('charge_name')->nullable();
            $table->string('description')->nullable();
            $table->decimal('amount');

            $table->timestamps();
            $table->softDeletes();
            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade');
            $table->foreign('unitcharge_id')->references('id')->on('unitcharges')->onDelete('cascade');
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
        Schema::dropIfExists('expense_items');
    }
};
