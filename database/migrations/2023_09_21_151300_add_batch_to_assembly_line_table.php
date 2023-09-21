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
        Schema::table('assembly_lines', function (Blueprint $table) {
            $table->string('from_batch')->nullable();
            $table->string('to_batch')->nullable();
            // $table->time('assembly_time')->nullable();
        });
    }
// 
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assembly_lines', function (Blueprint $table) {
            $table->dropColumn('from_batch');
            $table->dropColumn('to_batch');
            // $table->dropColumn('assembly_time');
        });
    }
};
