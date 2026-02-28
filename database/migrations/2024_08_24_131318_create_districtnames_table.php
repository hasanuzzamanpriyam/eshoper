<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistrictnamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('districtnames', function (Blueprint $table) {
            $table->id();
            $table->string('district_name_en', 50)->nullable();
            $table->string('district_name_bn', 50)->nullable();
            $table->string('district_shipping_charge', 20)->default('0');
            $table->string('status', 10)->nullable();
            $table->string('others', 20)->nullable();
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
        Schema::dropIfExists('districtnames');
    }
}
