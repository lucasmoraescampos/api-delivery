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
            $table->foreignId('company_id')->constrained('companies')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
            $table->foreignId('company_deliveryman_id')->nullable()->constrained('companies_deliverymen')->onUpdate('cascade')->onDelete('set null');
            $table->unsignedBigInteger('mercadopago_id')->nullable();
            $table->string('number', 20);
            $table->unsignedDecimal('price', 15, 2);
            $table->unsignedDecimal('total_price', 15, 2);
            $table->unsignedDecimal('delivery_price', 15, 2)->nullable();
            $table->unsignedDecimal('fee', 5, 1);
            $table->unsignedDecimal('online_payment_fee', 5, 1);
            $table->unsignedTinyInteger('type')->comment('DELIVERY = 1, WITHDRAWAL = 2');
            $table->unsignedTinyInteger('payment_type')->comment('ONLINE_PAYMENT = 1, PAYMENT_DELIVERY = 2');
            $table->unsignedDecimal('change_money', 15, 2)->nullable();
            $table->json('payment_method')->comment('{ name, icon }');
            $table->json('products')->comment('[{ name, qty, price }]');
            $table->json('delivery_location')->comment('{ street_name, street_number, district, complement, city, uf, latitude, longitude }')->nullable();
            $table->timestamp('delivery_forecast');
            $table->unsignedTinyInteger('delivery_type');
            $table->unsignedTinyInteger('status');
            $table->unsignedTinyInteger('evaluation')->nullable();
            $table->string('feedback', 255)->nullable();
            $table->string('reply', 255)->nullable();
            $table->timestamp('delivered_at')->nullable();
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
