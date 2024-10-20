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
        Schema::create('sms_topups', function (Blueprint $table) {
            $table->id()->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('transaction_code')->nullable();
            $table->decimal('amount');
            $table->decimal('received_credits');
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
        Schema::dropIfExists('sms_topups');
    }
};
