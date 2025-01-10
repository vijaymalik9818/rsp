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
        Schema::table('properties_all_data', function (Blueprint $table) {
            $table->dropColumn('LeaseAmount');
        });
    
        Schema::table('properties_all_data', function (Blueprint $table) {
            $table->string('ArchitecturalStyle', 255)->nullable();
            $table->string('LeaseAmount', 255);
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
