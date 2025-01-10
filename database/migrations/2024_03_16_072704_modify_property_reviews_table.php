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
        Schema::table('property_reviews', function (Blueprint $table) {
            $table->string('listing_id');

            $table->foreign('listing_id')->references('id')->on('listings')->onDelete('cascade');
            $table->string('listing_id')->after('id');
            $table->dropColumn('slug');
        });
      }
  
      /**
       * Reverse the migrations.
       */
      public function down()
      {
        Schema::table('property_reviews', function (Blueprint $table) {
            $table->dropColumn('listing_id');
            $table->string('slug')->after('id');
        });
      }
  };
