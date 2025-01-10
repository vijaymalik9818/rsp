<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeListPriceColumnTypeInPropertiesAllDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('properties_all_data', function (Blueprint $table) {
            // Change the datatype of 'ListPrice' column to DECIMAL with precision 10 and scale 2
            $table->decimal('ListPrice', 10, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('properties_all_data', function (Blueprint $table) {
            // If needed, you can revert the datatype change here
            // For example, if it was previously an integer, you could use:
            // $table->integer('ListPrice')->change();
        });
    }
}
