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

            $table->text('address')->nullable(false);

            $table->string('latitude', 40)->nullable(false);

            $table->string('longitude', 40)->nullable(false);

            $table->timestamp('created_at');

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
