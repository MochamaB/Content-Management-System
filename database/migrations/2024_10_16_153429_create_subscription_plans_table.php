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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id()->index();
            $table->string('plan_name')->nullable();
            $table->string('currency')->nullable();
            $table->decimal('price')->nullable();
            $table->unsignedTinyInteger('max_properties')->default(1);
            $table->unsignedTinyInteger('max_units')->default(20);
            $table->unsignedTinyInteger('max_users')->default(25);
           
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscription_plans');
    }
};
