<?php

// app/Services/ExcelExportService.php
namespace App\Services;

use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ExcelExportService implements FromCollection, WithHeadings, WithStyles
{
    protected $data;
    protected $resourceCollection;

    public function __construct(array $data, ResourceCollection $resourceCollection)
    {
        $this->data = $data;
        $this->resourceCollection = $resourceCollection;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        // Assuming the first item in the collection represents the modified data
        $modifiedData = $this->resourceCollection->first();

        return array_keys($modifiedData->toArray());
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFF00']]],
        ];
    }
}
