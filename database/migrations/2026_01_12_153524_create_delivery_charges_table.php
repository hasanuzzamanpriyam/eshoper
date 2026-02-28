<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('delivery_charges', function (Blueprint $table) {
            $table->id();
            $table->decimal('local_delivery_charge', 10, 2)->default(0);
            $table->decimal('country_delivery_charge', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_charges');
    }
};
