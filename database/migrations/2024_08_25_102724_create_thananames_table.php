<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThananamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thananames', function (Blueprint $table) {
            $table->id();
            $table->string('dist_id', 20)->nullable();
            $table->string('thana_name_en', 50)->nullable();
            $table->string('thana_name_bn', 50)->nullable();
            $table->string('thana_shipping_charge', 20)->default('0');
            $table->string('status', 10)->nullable();
            $table->string('district_name_eng', 50)->nullable();
            $table->string('district_name_bangla', 50)->nullable();
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
        Schema::dropIfExists('thananames');
    }
}
