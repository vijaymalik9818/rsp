<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailToReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Drop foreign key constraint referencing review_from column
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropForeign(['review_from']);
        });

        // Modify review_from column to be nullable
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('review_from')->nullable()->change();
        });

        // Add nullable email column
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('email')->nullable()->after('review_from');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop nullable email column
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('email');
        });

        // Modify review_from column to be non-nullable (if it was originally)
        Schema::table('reviews', function (Blueprint $table) {
            $table->string('review_from')->nullable(false)->change();
        });

        // Re-add foreign key constraint
        Schema::table('reviews', function (Blueprint $table) {
            $table->foreign('review_from')->references('id')->on('some_table')->onDelete('cascade');
            // Replace 'some_table' with the actual parent table name and 'id' with the actual primary key column name
        });
    }
}
