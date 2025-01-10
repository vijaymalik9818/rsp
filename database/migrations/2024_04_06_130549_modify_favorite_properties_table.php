<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyFavoritePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('favorite_properties', function (Blueprint $table) {
            // Change the data type of property_id to VARCHAR
            $table->string('property_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('favorite_properties', function (Blueprint $table) {
            // If you want to rollback the change, you can revert back to the original data type
            $table->unsignedBigInteger('property_id')->change();
        });
    }
}
