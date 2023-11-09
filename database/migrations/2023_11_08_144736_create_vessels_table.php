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
        Schema::create('vessels', function (Blueprint $table) {
            $table->id();
            $table->string('vessel_type');
            $table->string('vessel_no');
            $table->string('order_no');
            $table->string('part');
            $table->foreignIdFor(User::class);
            $table->unsignedBigInteger('packed_by')->references('id')->on('users')->nullable();
            $table->unsignedBigInteger('loaded_by')->references('id')->on('users')->nullable();
            $table->dateTime('loading_time')->nullable();
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
        Schema::dropIfExists('vessels');
    }
};
