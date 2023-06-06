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
        Schema::create('lines', function (Blueprint $table) {

            $table->string('order_no',20);
            $table->integer('line_no');
            $table->string('item_no',10);
            $table->string('item_description',100);
            $table->string('customer_spec',100);
            $table->string('posting_group',20);
            $table->string('part',5);
            $table->float('order_qty');
            $table->float('ass_qty');
            $table->float('exec_qty');
            $table->string('assembler',100);
            $table->string('checker',100);
            $table->primary(['order_no','line_no'])->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lines');
    }
};
