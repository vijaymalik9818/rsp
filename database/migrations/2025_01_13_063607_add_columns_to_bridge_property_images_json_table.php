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
        Schema::table('bridge_property_images_json', function (Blueprint $table) {
            $table->integer('last_processed_index')->nullable()->after('images_json')->comment('Tracks the last processed image index for resumable processing');
            $table->integer('processed_count')->nullable()->after('last_processed_index')->comment('Tracks the count of processed images');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bridge_property_images_json', function (Blueprint $table) {
            //
        });
    }
};
