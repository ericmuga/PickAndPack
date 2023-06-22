<?php

namespace App\Exports;

use App\Models\Item;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Helpers\ColumnListing;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ItemsExport implements FromCollection ,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //dd($columns);
        return Item::all('item_no','barcode','description','posting_group');
    }

    public function headings(): array
    {

     return  collect(new ColumnListing('items'))['columns'];

    }
}
