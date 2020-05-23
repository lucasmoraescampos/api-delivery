<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders_products', function (Blueprint $table) {

            $table->bigIncrements('id');

            $table->unsignedBigInteger('order_id')->nullable(false);

            $table->unsignedBigInteger('product_id')->nullable();

            $table->unsignedTinyInteger('qty')->nullable(false);

            $table->unsignedDecimal('unit_price', 15, 2)->nullable(false);

            $table->string('note', 150)->nullable();

            $table->foreign('order_id')
                ->references('id')
                ->on('orders')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
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
        Schema::dropIfExists('orders_products');
    }
}
