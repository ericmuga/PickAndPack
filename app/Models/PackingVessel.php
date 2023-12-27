<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingVessel extends Model
{
    use HasFactory;

   protected $guarded =['id'];

   public function packing_session_line()
   {
    return $this->hasMany(PackingSessionLine::class);
   }
}
