<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToPropertiesAllDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('properties_all_data', function (Blueprint $table) {
            $table->string('LeaseMeasure')->nullable();
            $table->decimal('LeaseAmount', 12, 2)->nullable();
            $table->string('LeaseAmountFrequency')->nullable();
            $table->string('PostalCode')->nullable();
            $table->string('BuildingType')->nullable();
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
            $table->dropColumn(['LeaseMeasure', 'LeaseAmount', 'LeaseAmountFrequency', 'PostalCode', 'PetsAllowed', 'BuildingType']);
        });
    }
}
