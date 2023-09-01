<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packing', function (Blueprint $table)
        {
            // $table->id();
            $table->string('order_no');
            $table->integer('line_no');
            $table->float('packed_qty');
            $table->foreignIdFor(User::class);
            $table->timestamps();
            $table->primary(['order_no','line_no']);
          });
            // $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packing');
    }
};
