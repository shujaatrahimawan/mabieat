<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPricingColumnsToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->float('retail_price_percentage', 10, 2)->nullable()->after('price');
            $table->float('wholesale_price_percentage', 10, 2)->nullable()->after('retail_price_percentage');
            $table->float('wholesale_price', 10, 2)->nullable()->after('wholesale_price_percentage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['retail_price_percentage', 'wholesale_price_percentage', 'wholesale_price']);
        });
    }
}
