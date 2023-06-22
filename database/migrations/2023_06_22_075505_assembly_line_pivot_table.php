<?php

use App\Models\User;
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
        Schema::create('assembly_lines', function (Blueprint $table) {

              $table->string('order_no');
              $table->integer('line_no');
              $table->float('ass_qty');
              $table->foreignIdFor(User::class);
              $table->timestamps();
              $table->primary(['order_no','line_no']);
            });
     }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assembly_lines');
    }
};
