<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubcomplements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcomplements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complement_id')->constrained('complements')->onUpdate('cascade')->onDelete('cascade');
            $table->string('description', 200);
            $table->unsignedDecimal('price', 15, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subcomplements');
    }
}
