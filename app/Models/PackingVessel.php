<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingVessel extends Model
{
    use HasFactory;

   public function packing_session()
   {
    return $this->belongsTo(PackingSession::class);
   }
}
