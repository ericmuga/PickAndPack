<?php

use App\Models\Assignment;
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
        Schema::create('assignment_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Assignment::class);
            $table->string('batch_no')->references('batch_no')->on('assignments');
            $table->string('order_no')->references('order_no')->on('orders');
            $table->string('part',1);
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
        Schema::dropIfExists('assignment_lines');
    }
};
