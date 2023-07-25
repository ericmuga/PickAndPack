<?php
// app/Traits/ExcelTrait.php

namespace App\Traits;

use App\Services\ExcelService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

trait ExcelTrait
{
    public function export($model, $fileName)
    {
        $excelService = new ExcelService($model);

        return $excelService->download($fileName);
        // return $excelService->store($fileName, 'public');
    }

    public function import(Request $request, $model)
    {
        $file = $request->file('import_file');

        // Add your logic to handle the imported file and save it to the respective model
        // You can use 'laravel-excel' to read the data from the file if needed

        // Example of reading data from the imported file
        $importedData = Excel::toArray([], $file);
        // Process the $importedData and save it to your model

        return redirect()->back()->with('success', 'Data imported successfully.');
    }
}
