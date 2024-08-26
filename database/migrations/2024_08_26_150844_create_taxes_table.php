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
            $table->string('model_type')->nullable();
            $table->decimal('rate')->nullable();
            $table->string('status')->nullable();
            $table->text('description')->nullable();

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
