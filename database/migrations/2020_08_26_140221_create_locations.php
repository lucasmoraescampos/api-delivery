<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLocations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('street_name', 255);
            $table->string('street_number', 20);
            $table->string('complement', 255)->nullable();
            $table->string('district', 100);
            $table->string('city', 100);
            $table->char('uf', 2);
            $table->string('postal_code', 20);
            $table->string('country', 100);
            $table->string('latitude', 40);
            $table->string('longitude', 40);
            $table->unsignedTinyInteger('type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }
}
