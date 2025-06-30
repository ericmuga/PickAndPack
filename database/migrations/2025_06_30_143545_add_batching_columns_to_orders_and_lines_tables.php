<?php

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
        // Add to 'orders' table
        Schema::table('orders', function (Blueprint $table) {
            $table->tinyInteger('is_fully_batched')->default(0);
        });

        // Add to 'lines' table
        Schema::table('lines', function (Blueprint $table) {
            $table->string('batch_number', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove from 'orders' table
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('is_fully_batched');
        });

        // Remove from 'lines' table
        Schema::table('lines', function (Blueprint $table) {
            $table->dropColumn('batch_number');
        });
    }
};
