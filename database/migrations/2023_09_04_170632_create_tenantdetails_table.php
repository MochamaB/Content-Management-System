<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantdetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenantdetails', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('user_relationship',100)->nullable();
            $table->string('emergency_name',100)->nullable();
            $table->string('emergency_number',100)->nullable();
            $table->string('emergency_email',100)->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('tenantdetails');
    }
}
