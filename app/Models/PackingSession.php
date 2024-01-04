<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\{Model,Builder};

class PackingSession extends Model
{
    use HasFactory;
    protected $guarded =['id'];

    public function user()
    {
      return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class,'order_no','order_no');
    }



    public function scopeOfPart(Builder $query, $part,$system=false) :void
    {
        $query->where('part',$part)
              ->where('system_entry',$system);
    }

    public function checker()
    {
        return $this->belongsTo(User::class,'checker_id','id');
    }

    public function lines()
    {
        return $this->hasMany(PackingSessionLine::class);
    }

    public function OrderLines()
    {
        return $this->hasMany(Line::class, 'order_no', 'order_no')->whereColumn('part', 'lines.part');
    }
     public function session_lines()
    {
        return $this->hasMany(PackingSessionLine::class);
    }

}
