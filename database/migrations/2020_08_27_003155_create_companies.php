<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('name', 150);
            $table->string('phone', 11);
            $table->string('document_number', 20);
            $table->unsignedDecimal('balance', 15, 2);
            $table->boolean('is_delivery_open');
            $table->unsignedTinyInteger('status');
            $table->string('slug', 255)->unique();
            $table->unsignedDecimal('evaluation', 2, 1)->nullable();
            $table->unsignedSmallInteger('waiting_time');
            $table->unsignedDecimal('delivery_price', 15, 2);
            $table->unsignedDecimal('min_order_value', 15, 2);
            $table->unsignedTinyInteger('radius');
            $table->boolean('allow_payment_delivery');
            $table->boolean('allow_payment_online');
            $table->boolean('allow_withdrawal_local');
            $table->string('image', 255);
            $table->string('street_name', 255);
            $table->string('street_number', 20);
            $table->string('complement', 255)->nullable();
            $table->string('district', 100);
            $table->string('city', 100);
            $table->char('uf', 2);
            $table->string('postal_code', 20);
            $table->string('country', 100)->nullable();
            $table->string('latitude', 40);
            $table->string('longitude', 40);
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
        Schema::dropIfExists('companies');
    }
}
