<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('segment_id')->nullable()->constrained('segments')->onUpdate('cascade')->onDelete('restrict');
            $table->string('name', 100);
            $table->string('description', 200);
            $table->unsignedSmallInteger('qty')->nullable();
            $table->unsignedDecimal('price', 15, 2);
            $table->unsignedDecimal('cost', 15, 2)->nullable();
            $table->unsignedDecimal('rebate', 15, 2)->nullable();
            $table->boolean('has_sunday');
            $table->boolean('has_monday');
            $table->boolean('has_tuesday');
            $table->boolean('has_wednesday');
            $table->boolean('has_thursday');
            $table->boolean('has_friday');
            $table->boolean('has_saturday');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('image', 255);
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
        Schema::dropIfExists('products');
    }
}
