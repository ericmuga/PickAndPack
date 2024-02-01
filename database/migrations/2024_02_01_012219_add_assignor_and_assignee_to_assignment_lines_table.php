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
        Schema::table('assignment_lines', function (Blueprint $table) {

            $table->unsignedBigInteger('assignee_id')->references('user_id')->on('users');
            $table->unsignedBigInteger('assignor_id')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assignment_lines', function (Blueprint $table) {
            $table->dropColumn('assignee_id');
            $table->dropColumn('assignor_id');
        });
    }
};
