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
        Schema::table('packing', function (Blueprint $table) {
            $table->integer('from_vessel')->nullable(); 
            $table->integer('to_vessel')->nullable();
            $table->string('vessel')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packing', function (Blueprint $table) {
           $table->dropColumn('from_vessel');
           $table->dropColumn('to_vessel');
           $table->dropColumn('vessel');
        });
    }
};
