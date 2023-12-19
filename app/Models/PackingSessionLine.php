<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use  Awobaz\Compoships\Compoships;

class PackingSessionLine extends Model
{
    use HasFactory;
    use Compoships;

    protected $guarded=['id'];

    public function session()
    {
        return $this->belongsTo(PackingSession::class);
    }

    public function order_line()
    {
        return $this->belongsTo(Line::class,'order_no','item_no');
    }

    public function item()
    {
        return $this->belongsTo(Item::class,'item_no','item_no');
    }
}
