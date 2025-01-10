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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->string('subject');
            $table->text('query');
            $table->string('attachment');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tickets');
    }
};
