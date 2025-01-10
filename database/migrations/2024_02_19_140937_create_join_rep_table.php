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
        Schema::create('join_rep', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone');
            $table->string('email');
            $table->boolean('joinee')->default(false);
            $table->string('experience')->nullable();
            $table->string('licensed_area')->nullable();
            $table->string('practice_areas')->nullable();
            $table->string('reference')->nullable();
            $table->text('about')->nullable();
            $table->boolean('is_contact')->default(false);
            $table->string('perceive')->nullable();
            $table->json('join_rep_data')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('join_rep');
    }
};
