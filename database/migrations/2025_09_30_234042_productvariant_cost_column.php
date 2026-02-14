<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProductvariantCostColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
                      Schema::table('product_variants', function (Blueprint $table) {
            $table->float('cost_percentage', 10, 2)->nullable();
       
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
            $table->dropColumn('cost_percentage');
        });
    }
}
