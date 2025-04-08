<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBatchedOrdersTable extends Migration
{
    public function up(): void
    {
        Schema::create('batched_orders', function (Blueprint $table) {
            $table->id();
            $table->integer('batch_number')->nullable();
            $table->string('sp_code');
            $table->string('sp_name');
            $table->date('shp_date');
            $table->string('item_no');
            $table->string('item_description');
            $table->string('part')->nullable();
            $table->float('order_qty');
            $table->timestamps();
        });

        Schema::create('batches', function (Blueprint $table) {
            $table->id();

            $table->timestamps();
        });


    }

    public function down(): void
    {
        Schema::dropIfExists('batched_orders');
        Schema::dropIfExists('batches');
    }
}
