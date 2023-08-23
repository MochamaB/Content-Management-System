<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_sites', function (Blueprint $table) {
            $table->id();
            $table->string('site_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_telephone')->nullable();
            $table->string('company_email')->nullable();
            $table->string('company_location')->nullable();
            $table->string('company_googlemaps')->nullable();
            $table->string('company_logo')->nullable();
            $table->string('company_flavicon')->nullable();
            $table->string('company_aboutus',200)->nullable();
            $table->string('site_currency')->nullable();
            $table->string('banner_desc',200)->nullable();
          
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('setting_sites');
    }
}
