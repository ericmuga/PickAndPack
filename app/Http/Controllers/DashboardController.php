<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use App\Models\{AssemblyLine, Line, Transfer,Order, PackingSessionLine, Stock};
use Illuminate\Http\Request;
use App\Services\SearchQueryService;
use Carbon\Carbon;
use App\Enums\CodeType;

class DashboardController extends Controller
{

    public  function getSectorTonnage($confirmed = false)
    {
        $codeMap = [
            CodeType::HORECA()->value => CodeType::HORECA()->codes,
            CodeType::RETAIL()->value => CodeType::RETAIL()->codes,
            CodeType::MSA()->value => CodeType::MSA()->codes,
            CodeType::UPC()->value => CodeType::UPC()->codes,
            CodeType::F_FOOD()->value => CodeType::F_FOOD()->codes,
            // Add other groups as needed
        ];

        $ordersWithSum = $this->getOrdersWithSum($confirmed);
        $sumBySpCode = $this->calculateSumBySpCode($ordersWithSum);
        $sectorTonnage = $this->calculateSectorTonnage($codeMap, $sumBySpCode);

        return $this->sortAndFormatSectorTonnage($sectorTonnage);
    }


private function getOrdersWithSum($confirmed)
{
    return Order::select('sp_code', 'order_no')
        ->current()
        ->when($confirmed, fn ($q) => $q->confirmed())
        ->groupBy('sp_code', 'order_no')
        ->withSum('lines', 'qty_base')
        ->get();
}

private function calculateSumBySpCode($ordersWithSum)
{
    return $ordersWithSum->groupBy('sp_code')
        ->map(function ($orders) {
            return $orders->sum('lines_sum_qty_base');
        });
}

private function calculateSectorTonnage($codeMap, $sumBySpCode)
{
    $sectorTonnage = [];

    foreach ($codeMap as $groupName => $groupCodes) {
        $sectorTonnage[$groupName] = round(collect($sumBySpCode)->only($groupCodes)->sum() / 1000, 2);
    }

    return $sectorTonnage;
}

private function sortAndFormatSectorTonnage($sectorTonnage)
{
    arsort($sectorTonnage);

    return $sectorTonnage;
}


}
