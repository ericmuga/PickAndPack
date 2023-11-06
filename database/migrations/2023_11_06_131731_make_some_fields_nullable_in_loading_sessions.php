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
        Schema::table('loading_sessions', function (Blueprint $table) {
            //
            // $table->string('assistant_loader_id')->nullable()->change()
            $table->unsignedBigInteger('assistant_driver_id')->references('id')->on('users')->nullable()->change();
            $table->unsignedBigInteger('loader_id')->references('users')->on('id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loading_sessions', function (Blueprint $table) {

            $table->unsignedBigInteger('assistant_driver_id')->references('id')->on('users');
            $table->unsignedBigInteger('loader_id')->references('users')->on('id');
        });
    }
};
