<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void */
    public function up()
    {
        Schema::table('packing', function (Blueprint $table) {
            $table->integer('from_batch')->nullable(); 
            $table->integer('to_batch')->nullable();
            // $table->string('vessel')->nullable();
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
           $table->dropColumn('from_batch');
           $table->dropColumn('to_batch');
           // $table->dropColumn('vessel');
        });
    }

};
