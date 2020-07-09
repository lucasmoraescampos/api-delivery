<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {

            $table->bigIncrements('id')->nullable(false);

            $table->unsignedBigInteger('company_id')->nullable(false);

            $table->unsignedBigInteger('menu_session_id')->nullable(false);

            $table->unsignedInteger('subcategory_id')->nullable(false);

            $table->string('name', 150)->nullable(false);

            $table->text('description')->nullable(false);

            $table->boolean('is_available_sunday')->nullable(false);

            $table->boolean('is_available_monday')->nullable(false);

            $table->boolean('is_available_tuesday')->nullable(false);

            $table->boolean('is_available_wednesday')->nullable(false);

            $table->boolean('is_available_thursday')->nullable(false);

            $table->boolean('is_available_friday')->nullable(false);

            $table->boolean('is_available_saturday')->nullable(false);

            $table->time('start_time')->nullable(true);

            $table->time('end_time')->nullable(true);

            $table->unsignedDecimal('price', 15, 2)->nullable(false);

            $table->unsignedDecimal('rebate', 15, 2)->nullable(true);

            $table->unsignedDecimal('promotional_price', 15, 2)->nullable(true);

            $table->string('photo', 255)->nullable(true);

            $table->unsignedTinyInteger('status')->nullable(false);

            $table->timestamps();

            $table->foreign('company_id')
                ->references('id')
                ->on('companies')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('menu_session_id')
                ->references('id')
                ->on('menu_sessions')
                ->onUpdate('cascade')
                ->onDelete('restrict');
                
            $table->foreign('subcategory_id')
                ->references('id')
                ->on('subcategories')
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
        Schema::dropIfExists('products');
    }
}
