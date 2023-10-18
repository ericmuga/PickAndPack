<?php

use App\Models\PackingSession;
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
        Schema::table('packing_sessions', function (Blueprint $table) {
            $table->boolean('system_entry')->default(true);

        });

        Schema::table('packing', function (Blueprint $table) {
            $table->foreignIdFor(PackingSession::class)->nullable();
            $table->dropColumn('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packing', function (Blueprint $table) {
            //
            $table->dropColumn('packing_session_id');
            $table->foreignIdFor(User::class)->nullable();
        });

         Schema::table('packing_sessions', function (Blueprint $table) {
            $table->dropColumn('system_entry');

        });
    }
};
