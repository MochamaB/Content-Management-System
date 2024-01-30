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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('property_id')->index();
            $table->unsignedBigInteger('vendorcategory_id')->index();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('phonenumber');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
            $table->foreign('vendorcategory_id')->references('id')->on('vendor_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vendors');
    }
};
