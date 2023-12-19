<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;


    protected $fillable=['item_no','barcode','description','posting_group'];


   public function prepacks()
   {

        return $this->hasMany(Prepack::class ,'item_no','item_no');
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class,'item_no','item_no');
    }

    public function lines()
    {
        return $this->hasMany(Line::class,'item_no','item_no');
    }

    public function packing_session_lines()
    {
        return $this->hasMany(PackingSessionLine::class,'item_no','item_no');
    }
}
