<?php

namespace App\Exports;

use App\Models\Confirmation;
use Maatwebsite\Excel\Concerns\FromCollection;

class ConfirmationExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Confirmation::all();
    }
}
