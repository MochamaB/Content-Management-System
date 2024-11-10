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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id()->index();
            $table->string('name');
            $table->string('command');
            $table->string('job_class')->nullable();
            $table->string('frequency')->nullable(); // you can use this to store 'monthlyOn' or 'twiceMonthly'
            $table->string('variable_one')->nullable(); // add this to store the day of the month to run the task
            $table->string('variable_two')->nullable(); // add this to store the second day of the month to run the task for 'twiceMonthly'
            $table->time('time')->nullable(); // you can use this to store the time to run the task
            $table->boolean('status')->nullable()->default(0);

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
        Schema::dropIfExists('tasks');
    }
};
