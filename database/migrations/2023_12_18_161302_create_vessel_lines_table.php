<?php

use App\Models\Vessel;
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
        Schema::create('vessel_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Vessel::class);
            $table->string('item_no')->index();
            $table->string('order_no')->index();
            $table->float('qty');
            $table->float('weight');
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
        Schema::dropIfExists('vessel_lines');
    }
};
