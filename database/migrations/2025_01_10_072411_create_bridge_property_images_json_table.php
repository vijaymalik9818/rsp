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
        if (!Schema::hasTable('bridge_property_images_json')) {
            Schema::create('bridge_property_images_json', function (Blueprint $table) {
                $table->id();
                $table->text('listing_id')->index();
                $table->json('images_json');
                $table->boolean('is_imported')->default(false);
                $table->timestamps();
            });
        } else {
            Schema::table('bridge_property_images_json', function (Blueprint $table) {
                if (!Schema::hasColumn('bridge_property_images_json', 'listing_id')) {
                    $table->text('listing_id')->index();
                }
                if (!Schema::hasColumn('bridge_property_images_json', 'images_json')) {
                    $table->json('images_json');
                }
                if (!Schema::hasColumn('bridge_property_images_json', 'is_imported')) {
                    $table->boolean('is_imported')->default(false);
                }
                if (!Schema::hasColumn('bridge_property_images_json', 'created_at')) {
                    $table->timestamps();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bridge_property_images_json');
    }
};
