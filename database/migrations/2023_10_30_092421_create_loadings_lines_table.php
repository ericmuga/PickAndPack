<?php

use App\Models\LoadingSession;
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
        Schema::create('loading_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(LoadingSession::class);
            $table->string('vessel_qr');
            $table->string('vessel');
            $table->string('vessel_no');
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
        Schema::dropIfExists('loading_lines');
    }
};
