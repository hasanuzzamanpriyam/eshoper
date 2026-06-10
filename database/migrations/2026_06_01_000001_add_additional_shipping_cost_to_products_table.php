<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddAdditionalShippingCostToProductsTable extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('additional_shipping_cost', 8, 2)->default(0)->after('shipping_cost');
        });

        // Migrate existing data: products with multiply_qty=1 get additional_shipping_cost = shipping_cost
        // products with multiply_qty=0 get additional_shipping_cost = 0 (flat rate)
        DB::statement("UPDATE products SET additional_shipping_cost = shipping_cost WHERE multiply_qty = 1 AND product_type = 'physical' AND shipping_cost > 0");
        DB::statement("UPDATE products SET additional_shipping_cost = 0 WHERE multiply_qty = 0 AND product_type = 'physical'");
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('additional_shipping_cost');
        });
    }
}
