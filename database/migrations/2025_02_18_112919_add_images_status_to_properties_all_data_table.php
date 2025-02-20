<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('properties_all_data', function (Blueprint $table) {
            $table->tinyInteger('images_status')->default(0); 
            $table->index('images_status');
        });
    }

    public function down(): void
    {
        Schema::table('properties_all_data', function (Blueprint $table) {
            $table->dropIndex(['images_status']);
            $table->dropColumn('images_status');
        });
    }
};
