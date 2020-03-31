<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100)->nullable(false);
            $table->string('surname', 100)->nullable(false);
            $table->string('email', 200)->nullable(false);
            $table->string('phone', 11)->nullable(false);
            $table->string('password', 255)->nullable(true);
            $table->unsignedTinyInteger('status')->nullable(true);
            $table->string('temporary_code', 25)->nullable(true);
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
        Schema::dropIfExists('users');
    }
}
