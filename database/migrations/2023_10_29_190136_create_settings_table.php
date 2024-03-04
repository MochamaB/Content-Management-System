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
            $table->string('category');
            $table->string('title');
            $table->string('settingable_type'); // E.g., "User", "Lease", "Property"
            $table->unsignedBigInteger('settingable_id'); // ID of the specific model instance
            $table->string('setting_name');
            $table->string('setting_value');
            $table->text('setting_description');

            $table->timestamps();
            $table->softDeletes();

            // Indexes for faster lookups
            $table->index(['settingable_type', 'settingable_id']);
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
