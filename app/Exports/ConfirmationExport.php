<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Carbon\Carbon;
class ConfirmationExport implements FromQuery, WithHeadings, ShouldAutoSize, WithEvents
{
    private $startDate;
    private $endDate;

    public function __construct(Request $request)
    {
        $this->startDate = Carbon::parse($request->start_date)->toDateString();
        $this->endDate = Carbon::parse($request->end_date)->toDateString();

    //    info($this->startDate);
    //    info($this->endDate);
    }

    public function query()
    {
        $query = DB::table('confirmations')
            ->select(
                'confirmations.id',
                'confirmations.created_at',
                'confirmations.part_no',
                'confirmations.user_id',
                'confirmations.order_no',
                'orders.customer_name',
                'orders.shp_name',
                'orders.sp_code',
                'orders.sp_name'
            )
            ->join('orders', 'orders.order_no', '=', 'confirmations.order_no')
            ->whereDate('confirmations.created_at','>=',$this->startDate)
            ->whereDate('confirmations.created_at','<=',$this->endDate)
            ->orderBy('id');

        // Log the SQL query
        //Log::info('Excel Export Query: ' . $query->toSql());

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Created At',
            'Part Number',
            'User ID',
            'Order Number',
            'Customer Name',
            'Salesperson Name',
            'Salesperson Code',
            'Ship-to Name',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Apply gray background to header row
                $event->sheet->getStyle('A1:I1')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => 'D3D3D3', // Gray color
                        ],
                    ],
                ]);
            },
        ];
    }
}
