<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_ip_restrictions', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45); // IPv4 + IPv6
            $table->timestamps();

            $table->timestamp('last_order_time');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_ip_restrictions');
    }
};
