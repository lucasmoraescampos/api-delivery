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

            $table->unsignedDecimal('total_price', 15, 2)->nullable(false);

            $table->unsignedTinyInteger('payment_type')->nullable(false);

            $table->unsignedInteger('payment_method_id')->nullable(true);

            $table->string('card_token', 255)->nullable(true);

            $table->string('card_last_number', 4)->nullable(true);

            $table->string('card_holder_name', 100)->nullable(true);

            $table->unsignedDecimal('cashback', 15, 2)->nullable(true);

            $table->unsignedDecimal('feedback', 5, 2)->nullable(true);

            $table->text('address')->nullable(false);

            $table->string('latitude', 40)->nullable(false);

            $table->string('longitude', 40)->nullable(false);

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

            $table->foreign('payment_method_id')
                ->references('id')
                ->on('payment_methods')
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
