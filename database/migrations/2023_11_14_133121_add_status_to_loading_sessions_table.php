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
            $table->string('status')->nullable();
            $table->boolean('system_entry')->nullable();
            $table->time('loading_time')->nullable();
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
            $table->dropColumn('system_entry');
            $table->dropColumn('loading_time');
            $table->dropColumn('status');
        });
    }
};
