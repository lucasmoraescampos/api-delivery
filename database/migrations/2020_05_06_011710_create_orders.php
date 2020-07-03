<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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

            $table->bigIncrements('id');

            $table->unsignedBigInteger('user_id')->nullable();

            $table->unsignedBigInteger('company_id')->nullable();

            $table->unsignedBigInteger('deliver_id')->nullable();

            $table->unsignedTinyInteger('payment_type')->nullable(false);

            $table->string('payment_method_id', 20)->nullable(false);

            $table->string('card_number', 20)->nullable(true);

            $table->string('card_holder_name', 100)->nullable(true);

            $table->text('address')->nullable(false);

            $table->string('latitude', 40)->nullable(false);

            $table->string('longitude', 40)->nullable(false);

            $table->unsignedDecimal('cashback', 15, 2)->nullable(true);

            $table->unsignedDecimal('price', 15, 2)->nullable(false);

            $table->unsignedDecimal('delivery_price', 15, 2)->nullable(true);

            $table->dateTime('delivery_forecast')->nullable(true);

            $table->unsignedDecimal('amount', 15, 2)->nullable(false);

            $table->unsignedDecimal('fee_meu_pedido', 15, 2)->nullable(false);

            $table->unsignedDecimal('fee_mercado_pago', 15, 2)->nullable(true);

            $table->unsignedTinyInteger('feedback')->nullable(true);

            $table->unsignedTinyInteger('status')->nullable(false);

            $table->timestamp('created_at');

            $table->dateTime('delivered_at')->nullable(true);

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onUpdate('cascade')
                ->onDelete('set null');

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onUpdate('cascade')
                ->onDelete('set null');

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
