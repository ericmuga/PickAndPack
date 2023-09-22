<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssemblySession extends Model
{
    use HasFactory;

   protected $guarded =['id'];

    public function user()
    {
      return $this->belongsTo(User::class);    
    }

    public function orders()
    {
        return $this->belongsTo(Order::class,'order_no','order_no');
    }

    public function line()
    {
        return $this->belongsTo(Line::class,'line_no','line_no');
    }
}
