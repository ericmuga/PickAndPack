<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('picks', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->timestamps();
        });

        Schema::table('lines',function(Blueprint $table)
        {
            $table->unsignedBigInteger('pick_id')
                   ->index()
                   ->references('picks')
                   ->on('id')
                   ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('picks');
        Schema::table('lines', function (Blueprint $table){
            // $table->dropIndex('picks');
            $table->dropColumn('pick_id');
        });
    }
};
