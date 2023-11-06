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
        Schema::table('packing_sessions', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('checker_id')->references('users')->on('id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packing_session', function (Blueprint $table) {
            //
            $table->dropConstrainedForeignId('checker_id');
            $table->dropColumn('checker_id');
        });
    }
};
