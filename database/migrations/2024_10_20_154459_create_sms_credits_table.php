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
        Schema::create('sms_credits', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedTinyInteger('credit_type')->default(1);
            $table->unsignedBigInteger('property_id')->nullable()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->decimal('tariff');
            $table->decimal('available_credits');
            $table->decimal('blocked_credits');
            $table->decimal('used_credits');
            $table->timestamps();

            $table->softDeletes();
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_credits');
    }
};
