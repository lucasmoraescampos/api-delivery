<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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

            $table->bigIncrements('id')->nullable(false);

            $table->unsignedBigInteger('complement_id')->nullable(false);

            $table->text('description')->nullable(false);

            $table->unsignedDecimal('price', 15, 2)->nullable(true);

            $table->foreign('complement_id')
                ->references('id')
                ->on('complements')
                ->onUpdate('cascade')
                ->onDelete('cascade');
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
