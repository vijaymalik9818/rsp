<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('property_sold_data', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ListingKeyNumeric')->unique();
            $table->string('UnparsedAddress')->nullable();
            $table->string('ListingId')->nullable();
            $table->decimal('ListPrice', 30, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('property_sold_data');
    }
};
