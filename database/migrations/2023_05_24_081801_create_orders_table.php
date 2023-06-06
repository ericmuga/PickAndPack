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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no',20)->unique()->index();
            $table->string('ended_by',100);
            $table->string('customer_no',10);
            $table->string('customer_name',250);
            $table->string('shp_code',10);
            $table->string('shp_name',250);
            $table->string('route_code',20);
            $table->string('sp_code',250);
            $table->string('sp_name',250);
            $table->date('shp_date');
            $table->string('assembler',100);
            $table->string('checker',100);
            $table->string('status');
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
        Schema::dropIfExists('orders');
    }
};
