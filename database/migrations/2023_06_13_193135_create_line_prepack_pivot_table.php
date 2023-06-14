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
        Schema::create('line_prepack_pivot', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('line_no');
            $table->unsignedBigInteger('prepack_name');
            $table->bigInteger('prepack_count');
            $table->bigInteger('total_quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('line_prepack_pivot');
    }
};
