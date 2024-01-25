<?php

namespace App\Exports;

use App\Models\PackingSession;

use Maatwebsite\Excel\Concerns\FromCollection;

class PackingSessionExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return         PackingSession::all();


    }
}
