<?php
namespace App\Services;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\FromArray;
use Illuminate\Database\Eloquent\Model;


use PhpOffice\PhpSpreadsheet\Style\Fill;

use Doctrine\DBAL\Schema\AbstractSchemaManager;


use Doctrine\DBAL\Types\Type;
use Illuminate\Support\Facades\Schema;


use Maatwebsite\Excel\Concerns\FromCollection;

trait JsonColumnsTrait
{
    private function getJsonColumns(Model $model): array
    {
        $jsonColumns = [];

        foreach (Schema::getColumnListing($model->getTable()) as $column) {
            if (Type::hasType('json') && Schema::getColumnType($model->getTable(), $column) === 'json') {
                $jsonColumns[] = $column;
            }
        }

        return $jsonColumns;
    }
}





class ExcelService implements WithMultipleSheets
{
    use Exportable,JsonColumnsTrait;

    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function sheets(): array
    {
        $sheets[] = new RegularSheet($this->model);

        // foreach ($this->getJsonColumns() as $jsonColumn) {
        //     $sheets[] = new JsonSheet($this->model, $jsonColumn);
        // }
        foreach ($this->getJsonColumns($this->model) as $jsonColumn) {
            $sheets[] = new JsonSheet($this->model, $jsonColumn);
        }
        return $sheets;
    }

    // private function getJsonColumns(): array
    // {
    //     $jsonColumns = [];

    //     foreach (Schema::getColumnListing($this->model->getTable()) as $column) {
    //         if (Type::hasType('json') && Schema::getColumnType($this->model->getTable(), $column) === 'json') {
    //             $jsonColumns[] = $column;
    //         }
    //     }

    //     return $jsonColumns;
    // }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:' . $event->sheet->getHighestColumn() . '1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => '000000',
                        ],
                    ],
                ]);

                $event->sheet->getStyle('A1:' . $event->sheet->getHighestColumn() . '1')->getAlignment()->setHorizontal('center');
            },
        ];
    }
}

class RegularSheet implements FromQuery, WithTitle, WithHeadings
{
    use Exportable,JsonColumnsTrait;

    private $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function query()
    {
        $nonJsonColumns = array_diff(
            Schema::getColumnListing($this->model->getTable()),
            $this->getJsonColumns($this->model)
        );

        return $this->model::query()->select($nonJsonColumns);
    }

    public function title(): string
    {
        return 'Regular Sheet';
    }

    public function headings(): array
    {
        return $this->getColumnNamesFormatted();
    }

    private function getColumnNamesFormatted(): array
    {
        $columns = Schema::getColumnListing($this->model->getTable());
        $formattedHeadings = [];

        foreach ($columns as $column) {
            if (!in_array($column, $this->getJsonColumns($this->model))) {
                $formattedHeadings[] = ucwords(str_replace('_', ' ', $column));
            }
        }

        return $formattedHeadings;
    }
}


class JsonSheet implements FromQuery, WithTitle, WithHeadings
{
    use Exportable,JsonColumnsTrait;

    private $model;
    private $jsonColumn;

    public function __construct(Model $model, string $jsonColumn)
    {
        $this->model = $model;
        $this->jsonColumn = $jsonColumn;
    }

    public function query()
    {
        return $this->model::query()->select(['id', $this->jsonColumn]);
    }

    public function title(): string
    {
        return 'JSON Sheet - ' . $this->jsonColumn;
    }

    public function headings(): array
    {
        $jsonData = json_decode($this->model->first()->{$this->jsonColumn});
        return $jsonData ? array_keys((array) $jsonData) : [];
    }
}




