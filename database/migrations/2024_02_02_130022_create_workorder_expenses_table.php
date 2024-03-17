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
        Schema::create('workorder_expenses', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('ticket_id')->index();
            $table->string('item');
            $table->decimal('quantity');
            $table->decimal('price');
            $table->decimal('amount');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('ticket_id')->references('id')->on('tickets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workorder_expenses');
    }
};
