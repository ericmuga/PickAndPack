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
    public function down()
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropColumn('part');
             // $table->dropForeign(['order_no']);
            $table->dropColumn('order_no');
            $table->json('order_parts');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('assignments', function (Blueprint $table) {
        
          $table->string('order_no')->references('order_no')->on('orders')->nullable();
          $table->string('part',10);
          $table->dropColumn('order_parts');

        });
    }
};
