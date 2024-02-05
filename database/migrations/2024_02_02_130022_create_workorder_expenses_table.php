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
            $table->unsignedBigInteger('request_id')->index();
            $table->decimal('quantity');
            $table->string('item');
            $table->decimal('price');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('request_id')->references('id')->on('requests')->onDelete('cascade');
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
