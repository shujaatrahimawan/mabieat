<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceFieldsToProductVariant extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->decimal('retail_price_percentage', 8, 2)->nullable();
            $table->decimal('wholesale_price_percentage', 8, 2)->nullable();
            $table->decimal('wholesale_price', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['retail_price_percentage', 'wholesale_price_percentage', 'wholesale_price']);
        });
    }
}
