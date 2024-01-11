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
        Schema::create('imported_orders', function (Blueprint $table) {
            $table->id();
            $table->string('company');
            $table->string('cust_no');
            $table->string('cust_spec');
            $table->string('ext_doc_no');
            $table->string('item_no');
            $table->string('item_spec');
            $table->unsignedBigInteger('line_no');
            $table->float('quantity');
            $table->string('shp_code');
            $table->date('shp_date');
            $table->string('sp_code');
            $table->string('uom_code');
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
        Schema::dropIfExists('imported_orders');
    }
};
