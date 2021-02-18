<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained('companies')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('table_id')->nullable()->constrained('tables')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('attendant_id')->nullable()->constrained('attendants')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('delivery_person_id')->nullable()->constrained('delivery_persons')->onUpdate('cascade')->onDelete('set null');
            $table->unsignedDecimal('price', 15, 2);
            $table->unsignedDecimal('total_price', 15, 2);
            $table->unsignedDecimal('delivery_price', 15, 2)->nullable();
            $table->unsignedDecimal('change_money', 15, 2)->nullable();
            $table->unsignedTinyInteger('type')->comment('LOCAL = 0, DELIVERY = 1, WITHDRAWAL = 2');
            $table->unsignedTinyInteger('payment_type')->comment('PAYMENT_LOCAL = 0, ONLINE_PAYMENT = 1, PAYMENT_DELIVERY = 2');
            $table->json('payment_method')->comment('{ name, icon, card_latest_numbers, card_holder }')->nullable();
            $table->json('products')->comment('[{ name, qty, price }]');
            $table->json('delivery_location')->comment('{ address, latitude, longitude }')->nullable();
            $table->timestamp('delivery_forecast')->nullable();
            $table->unsignedTinyInteger('status');
            $table->unsignedTinyInteger('evaluation')->nullable();
            $table->string('feedback', 255)->nullable();
            $table->string('reply', 255)->nullable();
            $table->string('additional_information', 500)->nullable();
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
        Schema::dropIfExists('orders');
    }
}
