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

            $table->bigIncrements('id');

            $table->unsignedTinyInteger('category_id');

            $table->string('name', 255);

            $table->string('email', 255)->unique();

            $table->string('phone', 20);

            $table->string('password', 255);

            $table->unsignedDecimal('balance_available', 15, 2);

            $table->unsignedDecimal('balance_receivable', 15, 2);

            $table->string('zipcode', 9)->nullable();

            $table->string('street_name', 255)->nullable();

            $table->string('street_number', 10)->nullable();

            $table->string('complement', 255)->nullable();

            $table->string('district', 200)->nullable();

            $table->string('city', 200)->nullable();

            $table->char('uf', 2)->nullable();

            $table->string('latitude', 40)->nullable();

            $table->string('longitude', 40)->nullable();

            $table->string('photo', 255)->nullable();

            $table->unsignedDecimal('min_value', 15, 2)->nullable();

            $table->unsignedDecimal('delivery_price', 15, 2)->nullable();

            $table->unsignedSmallInteger('waiting_time')->nullable();

            $table->unsignedMediumInteger('range')->nullable();

            $table->boolean('is_open');

            $table->boolean('accept_payment_app')->nullable();

            $table->boolean('accept_payment_delivery')->nullable();

            $table->boolean('accept_outsourced_delivery')->nullable();

            $table->boolean('accept_withdrawal_local')->nullable();

            $table->string('payment_methods', 100)->nullable();

            $table->unsignedDecimal('feedback', 5, 1)->nullable();

            $table->string('bank_name', 100)->nullable();

            $table->string('bank_agency', 20)->nullable();

            $table->unsignedTinyInteger('bank_type_account')->nullable();

            $table->string('bank_account', 20)->nullable();

            $table->string('bank_holder_name', 100)->nullable();

            $table->string('bank_document_number', 20)->nullable();

            $table->unsignedTinyInteger('status');

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
