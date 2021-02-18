<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesPaymentMethods extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies_payment_methods', function (Blueprint $table) {
            $table->foreignId('company_id')->constrained('companies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('payment_method_id')->constrained('payment_methods')->onUpdate('cascade')->onDelete('cascade');
            $table->primary(['company_id', 'payment_method_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies_payment_methods');
    }
}
