<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\{Driver, User,Vehicle};

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loading_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->foreignIdFor(Vehicle::class);
            $table->unsignedBigInteger('driver_id')->references('id')->on('users');
            $table->unsignedBigInteger('assistant_driver_id')->references('id')->on('users');
            $table->unsignedBigInteger('loader_id')->references('users')->on('id');
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
        Schema::dropIfExists('loading_sessions');
    }
};
