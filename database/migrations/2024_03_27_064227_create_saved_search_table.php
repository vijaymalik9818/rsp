<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSavedSearchTable extends Migration
{

    public function up()
    {
        Schema::create('saved_search', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title')->nullable();
            $table->string('duration')->nullable();
            $table->dateTime('sent_at')->nullable();
            $table->string('city')->nullable();
            $table->decimal('min_price', 10, 2)->nullable();
            $table->decimal('max_price', 10, 2)->nullable();
            $table->integer('beds')->nullable();
            $table->integer('bath')->nullable();
            $table->string('community')->nullable();
            $table->string('property_type')->nullable();
            $table->integer('min_sqft')->nullable();
            $table->integer('max_sqft')->nullable();
            $table->decimal('min_acres', 8, 2)->nullable();
            $table->decimal('max_acres', 8, 2)->nullable();
            $table->integer('min_yearbuilt')->nullable();
            $table->integer('max_yearbuilt')->nullable();
            $table->boolean('furnishedCheckbox')->default(false);
            $table->boolean('petsCheckbox')->default(false);
            $table->boolean('fireplace')->default(false);
            $table->boolean('onegarage')->default(false);
            $table->boolean('twogarage')->default(false);
            $table->boolean('threegarage')->default(false);
            $table->boolean('onestory')->default(false);
            $table->boolean('twostories')->default(false);
            $table->boolean('threestories')->default(false);
            $table->boolean('deck')->default(false);
            $table->boolean('basement')->default(false);
            $table->boolean('airconditioning')->default(false);
            $table->json('allColumns')->nullable();
            $table->timestamps();
            $table->softDeletes();

            
            $table->index(['city', 'community', 'property_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('saved_search');
    }
}
