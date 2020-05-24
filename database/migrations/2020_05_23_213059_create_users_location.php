<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersLocation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_locations', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id')->nullable(false);

            $table->string('street_name', 255)->nullable(false);

            $table->string('street_number', 20)->nullable(false);

            $table->string('complement', 255)->nullable(true);

            $table->string('district', 100)->nullable();

            $table->string('city', 100)->nullable(false);

            $table->char('uf', 2)->nullable(false);

            $table->string('postal_code', 20)->nullable(false);

            $table->string('country', 100)->nullable(false);

            $table->string('latitude', 40)->nullable(false);

            $table->string('longitude', 40)->nullable(false);

            $table->unsignedTinyInteger('type')->nullable(true);
            
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
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
        Schema::dropIfExists('users_locations');
    }
}
