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
        Schema::create('role_has_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->unsignedBigInteger('report_id');

            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('report_id')->references('id')->on('reports')->onDelete('cascade');

            $table->primary(['role_id', 'report_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_has_reports');
    }
};
