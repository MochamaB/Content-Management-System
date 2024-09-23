<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('model_type')->nullable(); // E.g., "User", "Lease", "Property"
            $table->unsignedBigInteger('model_id')->nullable(); // ID of the specific model instance
            $table->string('info');
            $table->string('name');
            $table->string('key');
            $table->text('value')->nullable();
            $table->text('description');

            $table->timestamps();
            $table->softDeletes();

            // Indexes for faster lookups
            $table->index(['model_type', 'model_id']);
            $table->index('name');
         
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
