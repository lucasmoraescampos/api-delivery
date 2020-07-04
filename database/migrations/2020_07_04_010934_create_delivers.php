<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDelivers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivers', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->string('name', 100)->nullable(false);

            $table->string('email', 200)->nullable(false)->unique();

            $table->string('phone', 11)->nullable(false)->unique();

            $table->string('password', 255)->nullable(true);

            $table->char('sms_code', 4)->nullable(true);

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
        Schema::dropIfExists('delivers');
    }
}
