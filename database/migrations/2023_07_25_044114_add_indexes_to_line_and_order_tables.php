<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('line_and_order_tables', function (Blueprint $table) {
        //     //
        // });

        Schema::table('lines', function (Blueprint $table) {
            $table->index('item_no');
            $table->index('item_description');
            // Add other indexes as needed for columns used in search conditions
        });

        Schema::table('orders', function (Blueprint $table) {
                $table->index('customer_name');
                $table->index('shp_name');
                $table->index('sp_name');
                $table->index('sp_code');
            });

        //     Schema::table('lines', function (Blueprint $table) {
        //     $table->foreign('order_no')->references('order_no')->on('orders');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

         Schema::table('lines', function (Blueprint $table) {
            $table->dropIndex('item_no');
            $table->dropIndex('item_description');
            // Add other indexes as needed for columns used in search conditions
        });
        // Schema::table('lines', function (Blueprint $table) {
        //     $table->dropForeign('order_no');//->references('order_no')->on('orders');
        // });

        Schema::table('orders', function (Blueprint $table) {
                $table->dropIndex('customer_name');
                $table->dropIndex('shp_name');
                $table->dropIndex('sp_name');
                $table->dropIndex('sp_code');
            });
    }
};
