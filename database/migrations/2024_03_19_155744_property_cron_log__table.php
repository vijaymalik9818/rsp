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
        Schema::create('properties_cron_log', function (Blueprint $table) {
            $table->id(); // Use auto-incrementing primary key
            $table->string('cron_file_name', 50)->notNull(); // Store cron file name
            $table->string('property_class', 50)->notNull(); // Specify property class
            $table->text('rets_query')->nullable(); // Allow nullable RETS query
            $table->dateTime('cron_start_time')->notNull(); // Record cron start time
            $table->dateTime('cron_end_time')->nullable(); // Optional cron end time
            $table->dateTime('properties_download_start_time')->nullable(); // Optional property download start time
            $table->dateTime('properties_download_end_time')->nullable(); // Optional property download end time
            $table->unsignedInteger('properties_count_from_mls')->default(0); // Unsigned integer for MLS property count
            $table->unsignedInteger('properties_count_actual_downloaded')->default(0); // Unsigned integer for downloaded count
            $table->string('property_inserted', 50)->nullable(); // Properties inserted (comma-separated or JSON?)
            $table->string('property_updated', 50)->nullable(); // Properties updated (comma-separated or JSON?)
            $table->string('steps_completed', 50)->notNull()->default('0'); // Track completed steps
            $table->boolean('force_stop')->default(false); // Use boolean for force stop flag
            $table->unsignedInteger('mls_no')->notNull(); // Unsigned integer for MLS number
            $table->tinyInteger('success')->default(0); // Use tinyInteger for success flag (0/1)
            $table->timestamps(); // Add created_at and updated_at columns
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

