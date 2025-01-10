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
        Schema::create('login_information', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->timestamp('login_timestamp')->default(now());
            $table->foreign('user_id')->references('id')->on('leads')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('login_information');
    }
};
