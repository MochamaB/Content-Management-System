<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('property_name');
            $table->string('property_slogan')->default('Where modern style meets comfort.')->nullable();
            $table->string('property_type');
            $table->string('property_location');
            $table->string('property_streetname');
            $table->string('property_description')->nullable();
            $table->boolean('property_status')->default(0);
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
        Schema::dropIfExists('properties');
    }
}
