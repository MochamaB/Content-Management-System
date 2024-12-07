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
        Schema::create('lease_items', function (Blueprint $table) {
            $table->id()->index();
            $table->foreignId('lease_id');
            $table->unsignedBigInteger('default_item_id')->nullable()->index();
            $table->enum('condition', ['Good', 'Needs Repair', 'Damaged', 'Needs Replacement']);
            $table->decimal('cost', 10, 2)->default(0.00);
            $table->timestamps();

            $table->softDeletes();
            $table->foreign('lease_id')->references('id')->on('leases')->onDelete('cascade');
            $table->foreign('default_item_id')->references('id')->on('default_lease_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lease_items');
    }
};
