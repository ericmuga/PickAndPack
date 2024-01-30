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
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('assembled')->nullable();
            $table->boolean('packed')->nullable();
            $table->boolean('loaded')->nullable();
            $table->boolean('cancelled')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('assembled');
            $table->dropColumn('packed');
            $table->dropColumn('loaded');
            $table->dropColumn('cancelled');

        });
    }
};
