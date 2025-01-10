<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeadsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['buy', 'sell', 'both']);
            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->string('password');
            $table->string('phone')->nullable();
            $table->text('about')->nullable();
            $table->string('facebook_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->enum('status', ['active', 'inactive'])->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('login_type')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
