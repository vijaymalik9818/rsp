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
        Schema::table('saved_search', function (Blueprint $table) {
            $table->string('just_listed')->nullable();
            $table->string('frontporch')->nullable();
            $table->string('patio')->nullable();
            $table->string('lake')->nullable();
            $table->string('playground')->nullable();
            $table->string('streetlights')->nullable();
            $table->string('pool')->nullable();
            $table->string('laundry')->nullable();
            $table->string('gazebo')->nullable();
            $table->string('clubhouse')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('saved_search', function (Blueprint $table) {
            //
        });
    }
};
