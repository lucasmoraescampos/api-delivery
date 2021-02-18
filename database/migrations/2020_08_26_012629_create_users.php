<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('uid', 100)->nullable()->unique();
            $table->string('customer_id', 100)->nullable()->unique();
            $table->string('name', 200);
            $table->string('email', 255)->unique();
            $table->string('phone', 11)->nullable()->unique();
            $table->string('password', 255)->nullable();
            $table->string('image', 255)->nullable();
            $table->unsignedTinyInteger('status');
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
