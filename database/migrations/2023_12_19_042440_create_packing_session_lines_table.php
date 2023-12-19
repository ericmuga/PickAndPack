<?php

use App\Models\PackingSession;
use App\Models\PackingVessel;
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
        Schema::create('packing_session_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(PackingSession::class);
            $table->string('order_no')->references('order_no')->on('lines');
            $table->string('item_no');
            // $table->integer('line_no')->references('line_no')->on('lines');
            $table->foreignIdFor(PackingVessel::class);
            $table->float('qty');
            $table->float('weight');
            $table->integer('from_vessel');
            $table->integer('to_vessel');
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
        Schema::dropIfExists('packing_session_lines');
    }
};
