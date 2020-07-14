<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesBankAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies_bank_accounts', function (Blueprint $table) {
            
            $table->bigIncrements('id');

            $table->unsignedSmallInteger('code');

            $table->string('agency', 20);

            $table->string('number', 20);

            $table->string('holder_name', 20);

            $table->string('document_number', 20);

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
        Schema::dropIfExists('companies_bank_accounts');
    }
}
