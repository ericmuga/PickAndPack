<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignmentLine extends Model
{
    use HasFactory;

    protected $table='assignment_lines';
    protected $guarded =[];


    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class,'order_no','order_no');
    }

   public function scopeOfPart($query,$part)
   {
       $query->where('part','LIKE','%'.$part);
   }
}
