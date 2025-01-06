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
            $table->string('plan_name')->index();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('currency', 3);
            $table->decimal('price', 10, 2);
            $table->integer('max_properties')->nullable();
            $table->integer('max_units')->nullable();
            $table->integer('max_users')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('modules')->nullable();
            $table->json('features')->nullable();
            $table->string('billing_cycle')->default('monthly');
            $table->integer('grace_days')->nullable();
            $table->timestamps();
            $table->softDeletes();
           
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
