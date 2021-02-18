<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliveryPersons extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_persons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name', 150);
            $table->string('additional_information', 2000)->nullable();
            $table->boolean('status');
            $table->string('image', 255)->nullable();
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
        Schema::dropIfExists('delivery_persons');
    }
}
