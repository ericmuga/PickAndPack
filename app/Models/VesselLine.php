<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VesselLine extends Model
{
    use HasFactory;

    protected $guarded =['id'];

    public function vessel ()
    {
        return $this->belongsTo(Vessel::class);
    }
}
