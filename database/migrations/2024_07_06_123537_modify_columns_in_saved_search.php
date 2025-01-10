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
            // Changing columns type to tinyint(1)
            $table->tinyInteger('just_listed')->nullable()->change();
            $table->tinyInteger('frontporch')->nullable()->change();
            $table->tinyInteger('patio')->nullable()->change();
            $table->tinyInteger('lake')->nullable()->change();
            $table->tinyInteger('playground')->nullable()->change();
            $table->tinyInteger('streetlights')->nullable()->change();
            $table->tinyInteger('pool')->nullable()->change();
            $table->tinyInteger('laundry')->nullable()->change();
            $table->tinyInteger('gazebo')->nullable()->change();
            $table->tinyInteger('clubhouse')->nullable()->change();
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
