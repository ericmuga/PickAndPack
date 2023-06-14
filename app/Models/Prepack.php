<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prepack extends Model
{
    use HasFactory;

    protected $fillable=['name','pack_size','item_no','isActive'];


    public function lines()
    {
        return $this->belongsToMany(Line::class,'line_prepack_pivot','prepack_name','line_id');
    }



    public function item()
    {
        return $this->belongsTo(Item::class,'item_no','item_no');
    }

}
