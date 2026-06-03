<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerReputationsTable extends Migration
{
    public function up()
    {
        Schema::create('customer_reputations', function (Blueprint $table) {
            $table->id();
            $table->string('phone')->unique();
            $table->json('data');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer_reputations');
    }
}
