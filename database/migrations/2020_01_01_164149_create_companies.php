<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {

            $table->bigIncrements('id')->nullable(false);

            $table->unsignedTinyInteger('category_id')->nullable(false);

            $table->string('name', 255)->nullable(false);

            $table->string('email', 255)->nullable(false)->unique();

            $table->string('phone', 20)->nullable(false);

            $table->string('password', 255)->nullable(false);

            $table->unsignedDecimal('balance_available', 15, 2)->nullable(false);

            $table->unsignedDecimal('balance_receivable', 15, 2)->nullable(false);

            $table->string('zipcode', 9)->nullable(true);

            $table->string('street_name', 255)->nullable(true);

            $table->string('street_number', 10)->nullable(true);

            $table->string('complement', 255)->nullable(true);

            $table->string('district', 200)->nullable(true);

            $table->string('city', 200)->nullable(true);

            $table->char('uf', 2)->nullable(true);

            $table->string('latitude', 40)->nullable(true);

            $table->string('longitude', 40)->nullable(true);

            $table->string('photo', 255)->nullable(true);

            $table->unsignedDecimal('min_value', 15, 2)->nullable(true);

            $table->unsignedDecimal('delivery_price', 15, 2)->nullable(true);

            $table->unsignedSmallInteger('waiting_time')->nullable(true);

            $table->unsignedMediumInteger('range')->nullable(true);

            $table->boolean('is_open')->nullable(false);

            $table->boolean('accept_payment_app')->nullable(true);

            $table->boolean('accept_outsourced_delivery')->nullable(true);

            $table->boolean('accept_withdrawal_local')->nullable(true);

            $table->unsignedDecimal('feedback', 5, 1)->nullable(true);

            $table->unsignedTinyInteger('status')->nullable(false);

            $table->timestamps();

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onUpdate('cascade')
                ->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
