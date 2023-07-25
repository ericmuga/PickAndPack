<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('confirmed')->nullable()->default(false);
        });

        $this->optimizeOrders();


    }

    public function optimizeOrders()
{
    // Subquery to calculate confirmations count for each order
    $subqueryConfirmations = DB::table('confirmations')
        ->select('order_no')
        ->selectRaw('COUNT(DISTINCT id) as confirmations_count')
        ->groupBy('order_no');

    // Subquery to calculate parts count for each order
    $subqueryParts = DB::table('lines')
        ->select('order_no')
        ->selectRaw('COUNT(DISTINCT part) as parts_count')
        ->groupBy('order_no');

    // Perform the update with JOIN on the subqueries
    DB::table('orders')
        ->leftJoinSub($subqueryConfirmations, 'confirmations', 'orders.order_no', '=', 'confirmations.order_no')
        ->leftJoinSub($subqueryParts, 'lines', 'orders.order_no', '=', 'lines.order_no')
        ->update([
            'confirmed' => DB::raw('CASE WHEN parts_count = confirmations_count THEN 1 ELSE 0 END')
        ]);
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('confirmed');
        });
    }
};
