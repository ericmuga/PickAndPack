<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;
   protected $guarded =['id'];

   public function assignor()
   {
    return $this->belongsTo(User::class,'assignor_id','id');
   }

   public function assignee()
   {
    return $this->belongsTo(User::class,'assignee_id','id');
   }


   public function lines()
   {
    return $this->hasMany(AssignmentLine::class);
   }

   public function assembly_session()
   {
    return $this->hasMany(AssemblySession::class);
   }




}
