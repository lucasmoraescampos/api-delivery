<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVouchers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->unsignedBigInteger('company_id')->nullable();

            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('code', 40);

            $table->unsignedSmallInteger('qty');

            $table->unsignedDecimal('value', 15, 2);

            $table->unsignedDecimal('min_value', 15, 2);

            $table->dateTime('expiration_date');

            $table->unsignedTinyInteger('status');

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('set null');

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
        Schema::dropIfExists('vouchers');
    }
}
