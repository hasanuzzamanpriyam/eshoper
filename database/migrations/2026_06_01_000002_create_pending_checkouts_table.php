<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePendingCheckoutsTable extends Migration
{
    public function up()
    {
        Schema::create('pending_checkouts', function (Blueprint $table) {
            $table->id();
            $table->string('customer_type')->default('guest')->comment('registered or guest');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('guest_id')->nullable();
            $table->string('contact_person_name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('shipping_address');
            $table->string('city')->nullable();
            $table->string('thana')->nullable();
            $table->string('zip')->nullable();
            $table->string('country')->nullable();
            $table->text('billing_address')->nullable();
            $table->text('order_comment')->nullable();
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->string('status')->default('pending')->comment('pending, paid, abandoned');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pending_checkouts');
    }
}
