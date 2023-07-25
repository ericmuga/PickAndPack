<?php
// app/Traits/ExcelExportTrait.php
namespace App\Traits;

use App\Services\ExcelExportService;
use Maatwebsite\Excel\Facades\Excel;

trait ExcelExportTrait
{
    public function exportExcel($data, $resourceCollectionClass, $filename)
    {
        $resourceCollection = $resourceCollectionClass::collection($data);

        $exportService = new ExcelExportService($data, $resourceCollection);

        return Excel::download($exportService, $filename);
    }
}
