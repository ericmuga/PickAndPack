<?php

use App\Models\AssemblySession;
use App\Models\Assignment;
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
        Schema::table('assembly_lines', function (Blueprint $table) {
            // $table->dropColumn('user_id');
            $table->foreignIdFor(AssemblySession::class)->nullable();

        });

         Schema::table('assembly_sessions', function (Blueprint $table) {

            $table->foreignIdFor(Assignment::class)->nullable();
            $table->boolean('system_entry')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('assembly_lines', function (Blueprint $table)
        {
            // $table->foreignIdFor(User::class)->nullable();
            $table->dropColumn('assembly_session_id');
        });


         Schema::table('assembly_sessions', function (Blueprint $table) {

            $table->dropColumn('assignment_id');
            $table->dropColumn('system_entry');
        });
    }
};
