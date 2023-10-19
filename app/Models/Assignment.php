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

   public function orderCount()
   {
      return DB::table('assignments as a')
               ->join('assignment_lines  as b','b.assignment_id','a.id')
               ->where('a.id',$this->id)
               ->select('b.order_no')
               ->distinct()
               ->count();
   }

   public function productLineCount()
   {

     //returns the product lines in the assignment
     return DB::table('assignments as a')
              ->join('assignment_lines  as b','b.assignment_id','a.id')
              ->join('lines as c', fn($join)=>$join->on('c.order_no','b.order_no')->on('c.part','b.part'))
              ->where('a.id',$this->id)
              ->count();


   }


   public function totalTime()
   {

    $totalTime= DB::table('assembly_sessions')
                 ->where('assignment_id',$this->id)
                 ->selectRaw('SUM(CONVERT(INT, DATEDIFF(SECOND, 0, assembly_time))/ 3600) AS total_hours')
                 ->selectRaw('SUM(CONVERT(INT, DATEDIFF(SECOND, 0, assembly_time)) % 3600 / 60) AS total_minutes')
                 ->selectRaw('SUM(CONVERT(INT, DATEDIFF(SECOND, 0, assembly_time)) % 60) AS total_seconds')
                 ->get();

    $totalHours = $totalTime[0]->total_hours;
    $totalMinutes = $totalTime[0]->total_minutes;
    $totalSeconds = $totalTime[0]->total_seconds;
    return sprintf("%02d:%02d:%02d", $totalHours, $totalMinutes, $totalSeconds);
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


   }

   public function percentage()
   {
    //get the lines in the assignment
      if($this->productLineCount()==0)return 0;
      return round($this->assembledProductLines()/$this->productLineCount()*100);

    //get the percentage of the total lines
   }




}
