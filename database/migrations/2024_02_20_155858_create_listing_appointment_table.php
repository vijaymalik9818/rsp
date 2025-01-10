<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('listing_appointments', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('property_address')->nullable();
            $table->string('community')->nullable();
            $table->string('approx_age_of_property')->nullable();
            $table->string('approx_size_of_property')->nullable();
            $table->string('style_of_property')->nullable();
            $table->string('no_of_bedrooms')->nullable();
            $table->string('no_of_bathrooms')->nullable();
            $table->string('basement_development')->nullable();
            $table->string('parking')->nullable();
            $table->string('interest')->nullable();
            $table->text('additional_information')->nullable();
            $table->json('listing_appointments_data')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('listing_appointment');
    }
};
