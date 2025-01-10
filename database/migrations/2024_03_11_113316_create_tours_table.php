<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateToursTable extends Migration
{
    public function up()
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('time');
            $table->string('phone');
            $table->string('email');
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tours');
    }
}
