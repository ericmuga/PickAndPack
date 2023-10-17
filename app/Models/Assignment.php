<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Line;
use Illuminate\Support\Facades\DB;

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

   public function assignedProductLines()
   {

    //returns the product lines in the assignment
    $assignedLineCount=0;
    foreach($this->lines()->get() as $line)
    {
        // dd($line);
      $assignedLineCount+=Line::where('order_no',$line->order_no)
                        ->where('part',$line->part)
                        ->count();

    }

    //if ($this->id==21) dd($assignedLineCount);
    return $assignedLineCount;
   }


   public function assembledProductLines()
   {

    //returns the product lines in the assignment
      return  DB::table('assembly_lines')
                ->join('assignment_lines','assembly_lines.order_no','assignment_lines.order_no')
                ->join('assignments','assignment_lines.assignment_id','assignments.id')
                ->join ('lines',fn($join)=>$join->on('lines.order_no','assignment_lines.order_no')
                                ->on('lines.line_no','assembly_lines.line_no')
                                ->on('lines.part','assignment_lines.part'))
                ->where('assignments.id',$this->id)
                ->count();


//  if ($this->id==21) dd($assembledLineCount);

   }

   public function percentage()
   {
    //get the lines in the assignment
      if($this->assignedProductLines()==0)return 0;
      return round($this->assembledProductLines()/$this->assignedProductLines()*100);

    //get the percentage of the total lines
   }




}
