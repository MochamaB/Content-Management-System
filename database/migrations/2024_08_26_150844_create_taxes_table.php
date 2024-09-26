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
        Schema::create('taxes', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('property_type_id')->index();
            $table->string('name')->nullable();
            $table->string('taxable_type')->nullable();
            $table->unsignedBigInteger('taxable_id')->nullable(); // ID of the specific model instance
            $table->decimal('rate')->nullable();
            $table->string('status')->nullable();
            $table->text('description')->nullable();
            $table->string('related_model_type')->nullable();
            $table->json('related_model_condition')->nullable();
            $table->json('additional_condition')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->foreign('property_type_id')->references('id')->on('property_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taxes');
    }
};
