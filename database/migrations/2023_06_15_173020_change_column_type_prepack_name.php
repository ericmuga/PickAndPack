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
        Schema::table('line_prepack_pivot', function (Blueprint $table) {
            $table->string('prepack_name', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('line_prepack_pivot', function (Blueprint $table) {
            $table->unsignedBigInteger('prepack_name')->change();
        });

    }
};
