<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanSubscriptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('plan_id')->constrained('plans')->onUpdate('cascade')->onDelete('restrict');
            $table->string('payment_id', 255)->comment('Mercado Pago Payment');
            $table->unsignedDecimal('transaction_amount', 15, 2);
            $table->unsignedDecimal('transaction_fee', 15, 2)->nullable();
            $table->datetime('expiration');
            $table->boolean('status');
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
        Schema::dropIfExists('plan_subscriptions');
    }
}
