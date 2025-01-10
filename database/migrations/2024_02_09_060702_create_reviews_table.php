<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_from');
            $table->foreign('review_from')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('review_to');
            $table->foreign('review_to')->references('id')->on('users')->onDelete('cascade');
            $table->text('review_feedback')->nullable();
            $table->decimal('rating', 3, 1)->nullable(); // Decimal with one decimal place, e.g., 1.5, 2.5
            $table->decimal('avg_rating', 3, 1)->nullable(); // Decimal with one decimal place, e.g., 1.5, 2.5
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
        Schema::dropIfExists('reviews');
    }
}

