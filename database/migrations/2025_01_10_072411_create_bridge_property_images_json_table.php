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
        Schema::create('bridge_property_images_json', function (Blueprint $table) {
            $table->id(); 
            $table->string('listing_id')->index(); 
            $table->json('images_json'); 
            $table->boolean('is_imported')->default(false); 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bridge_property_images_json');
    }
};
