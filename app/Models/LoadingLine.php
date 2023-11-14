<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoadingLine extends Model
{
    use HasFactory;
 protected $table='loading_lines';

 protected $guarded=['id'];

 public function session(){
    return $this->belongsTo(LoadingSession::class);
 }


}
