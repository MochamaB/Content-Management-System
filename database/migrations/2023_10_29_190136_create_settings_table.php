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
            $table->string('module');
            $table->string('submodule'); // E.g., "User", "Lease", "Property"
            $table->unsignedBigInteger('model_id'); // ID of the specific model instance
            $table->string('setting_name');
            $table->text('setting_value');
            $table->timestamps();

            // Indexes for faster lookups
            $table->index(['submodule', 'model_id']);
            $table->index('setting_name');
         
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
