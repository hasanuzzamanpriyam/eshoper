<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCartItemsToPendingCheckoutsTable extends Migration
{
    public function up()
    {
        Schema::table('pending_checkouts', function (Blueprint $table) {
            $table->json('cart_items')->nullable()->after('total_amount');
        });
    }

    public function down()
    {
        Schema::table('pending_checkouts', function (Blueprint $table) {
            $table->dropColumn('cart_items');
        });
    }
}
