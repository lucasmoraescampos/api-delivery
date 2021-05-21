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
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('plan_id')->constrained('plans')->onUpdate('cascade')->onDelete('restrict');
            $table->string('name', 150);
            $table->string('phone', 11);
            $table->string('document_number', 20);
            $table->unsignedDecimal('balance', 15, 2);
            $table->string('slug', 255)->unique();
            $table->unsignedDecimal('evaluation', 2, 1)->nullable();
            $table->unsignedSmallInteger('delivery_time');
            $table->unsignedDecimal('delivery_price', 15, 2);
            $table->unsignedDecimal('min_order_value', 15, 2);
            $table->unsignedDecimal('radius', 5, 1);
            $table->boolean('allow_payment_delivery');
            $table->boolean('allow_payment_online');
            $table->boolean('allow_takeout');
            $table->string('image', 255);
            $table->string('banner', 255)->nullable();
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
            $table->unsignedTinyInteger('status');
            $table->boolean('open');
            $table->boolean('deleted');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
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
