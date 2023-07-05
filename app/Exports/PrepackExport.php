<?php
namespace App\Exports;

use App\Models\LinePrepack;
use App\Http\Resources\LinePrepackResource;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\WithEvents;
// use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;

class PrepackExport implements FromCollection, WithHeadings, WithMapping,WithEvents
{
    // ...

    private $parameters;

    public function __construct($parameters)
    {
        $this->parameters = $parameters;
    }

    public function collection()
    {
        $model = new LinePrepack();
        $query = $model->newQuery();
        $tableName = $model->getTable();

        if (!empty($this->parameters)) {
            foreach ($this->parameters['requestData'] as $column => $value) {
                if ($value != '') {
                    if (Schema::hasColumn($tableName, $column))
                    {
                        if (is_array($value)) {
                            if (Schema::getColumnType($tableName, $column) == 'datetime') {
                                $query->whereBetween($column, $value);
                            } else {
                                $query->whereIn($column, $value);
                            }
                        } else {
                            $query->where($column, $value);
                        }
                    }
                    else{
                        if ($value=='sp_code')
                           $query->orWhereHas('order', fn($q)=>$q->whereIn('sp_code',$value));
                        if ($value=='shp_date')
                           $query->orWhereHas('order', fn($q)=>$q->whereBetween('shp_date',$value));
                    }
                }
            }
        }



        return $query->with('user','order','line')->get();
    }

    public function headings(): array
    {
        // Define the column headings for the Excel export
        return [
            'id',
            'prepack_name',
            'item_description',
            'total_quantity',
            'prepack_count',
            'batch_no',
            'order_no',
            'carton_no',
            'prepack_time',
            'prepared_by',
            'customer',
            'shp_name',
            'sales_person',


            ];
    }

    public function map($row): array
    {
        // Map each row of the collection to the desired format
        return [
            $row->id,
            $row->prepack_name,
            $row->line->toArray()['item_description'],
            $row->total_quantity,
            $row->prepack_count,
            $row->batch_no,
            $row->order_no,
            $row->carton_no,
            $row->created_at,
            $row->user->toArray()['name'],
            $row->order->toArray()['customer_no'].'|'.$row->order->toArray()['customer_name'],
            $row->order->toArray()['shp_name'],
            $row->order->toArray()['sp_code'].'|'.$row->order->toArray()['sp_name']


            // $row->line->toArray(), // Assuming LineResource also has a toArray() method
            , // Assuming OrderResource also has a toArray() method
            // $row->user->toArray()->name
            // Map additional columns based on your resource class
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                 // Replace 'FF0000' with your desired color code
            },
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:M1')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'F0F0F0' // Replace 'FF0000' with your desired color code
                        ]
                    ],
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'] // Replace '000000' with your desired border color code
                        ]
                    ],
                ]);
            },
        ];
    }
}
