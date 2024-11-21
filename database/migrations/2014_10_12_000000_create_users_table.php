<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('password_set')->default(false);
            $table->string('phonenumber',100)->nullable();
            $table->string('idnumber',100)->nullable();
            $table->string('status')->nullable();

            // Two-Factor Authentication Columns
            $table->string('two_factor_secret')->nullable();
            $table->string('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();

              // Social Login Columns
            $table->string('provider')->nullable(); // like 'google', 'facebook'
            $table->string('provider_id')->nullable(); // unique ID from provider
            
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
