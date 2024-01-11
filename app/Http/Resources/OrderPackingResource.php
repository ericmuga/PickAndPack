<?php

namespace App\Http\Resources;

use App\Models\Confirmation;
use Illuminate\Support\Carbon;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderPackingResource extends JsonResource
{
    /**
    * Transform the resource into an array.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
    */
    public function toArray($request)
    {

       $a_vessels=DB::table('packing_session_lines')
                    ->join('packing_sessions', 'packing_sessions.id', 'packing_session_lines.packing_session_id')
                    ->join('orders', 'orders.order_no','packing_sessions.order_no')
                    ->join('packing_vessels', 'packing_vessels.id', 'packing_session_lines.packing_vessel_id')
                    ->where('packing_sessions.part', 'A')
                    ->where('packing_sessions.order_no', $this->order_no)
                    ->select('packing_vessels.code')
                    ->addSelect(DB::raw('SUM(packing_session_lines.to_vessel - packing_session_lines.from_vessel + 1) as vessel_count'))
                    ->groupBy('packing_vessels.code')
                    ->get();

       $b_vessels=DB::table('packing_session_lines')
                    ->join('packing_sessions', 'packing_sessions.id', 'packing_session_lines.packing_session_id')
                    ->join('orders', 'orders.order_no','packing_sessions.order_no')
                    ->join('packing_vessels', 'packing_vessels.id', 'packing_session_lines.packing_vessel_id')
                    ->where('packing_sessions.part', 'B')
                    ->where('packing_sessions.order_no', $this->order_no)
                    ->select('packing_vessels.code')
                    ->addSelect(DB::raw('SUM(packing_session_lines.to_vessel - packing_session_lines.from_vessel + 1) as vessel_count'))
                    ->groupBy('packing_vessels.code')
                    ->get();
        $c_vessels=DB::table('packing_session_lines')
                    ->join('packing_sessions', 'packing_sessions.id', 'packing_session_lines.packing_session_id')
                    ->join('orders', 'orders.order_no','packing_sessions.order_no')
                    ->join('packing_vessels', 'packing_vessels.id', 'packing_session_lines.packing_vessel_id')
                    ->where('packing_sessions.part', 'C')
                    ->where('packing_sessions.order_no', $this->order_no)
                    ->select('packing_vessels.code')
                    ->addSelect(DB::raw('SUM(packing_session_lines.to_vessel - packing_session_lines.from_vessel + 1) as vessel_count'))
                    ->groupBy('packing_vessels.code')
                    ->get();
        $d_vessels=DB::table('packing_session_lines')
                    ->join('packing_sessions', 'packing_sessions.id', 'packing_session_lines.packing_session_id')
                    ->join('orders', 'orders.order_no','packing_sessions.order_no')
                    ->join('packing_vessels', 'packing_vessels.id', 'packing_session_lines.packing_vessel_id')
                    ->where('packing_sessions.part', 'D')
                    ->where('packing_sessions.order_no', $this->order_no)
                    ->select('packing_vessels.code')
                    ->addSelect(DB::raw('SUM(packing_session_lines.to_vessel - packing_session_lines.from_vessel + 1) as vessel_count'))
                    ->groupBy('packing_vessels.code')
                    ->get();

    //  dd($b_vessels);



        return [

            'order_no'=>$this->order_no,
                'ext_doc_no'=>$this->ext_doc_no,
            'ended_by'=>Str::replace('FARMERSCHOICE\\','',$this->ended_by),
            'customer_no'=>$this->customer_no,
            'customer_name'=>$this->customer_name,
            'shp_code'=>$this->shp_code,
            'shp_name'=>$this->shp_name,
            'sp_name'=>$this->sp_name,
            'sp_code'=>$this->sp_code,
            'sp_search_name'=>$this->sp_code.'|'.$this->sp_name,
            'shp_date'=>Carbon::parse($this->shp_date)->toDateString(),
            'part_a'=>$this->lines()->OfPart('A')->count(),
            'part_b'=>$this->lines()->OfPart('B')->count(),
            'part_c'=>$this->lines()->OfPart('C')->count(),
            'part_d'=>$this->lines()->OfPart('D')->count(),
            'vessel_a'=>$a_vessels,
            'vessel_b'=>$b_vessels,
            'vessel_c'=>$c_vessels,
            'vessel_d'=>$d_vessels,
            'confirm_a'=>Order::checkConfirmation($this->order_no,'A'),
            'confirm_b'=>Order::checkConfirmation($this->order_no,'B'),
            'confirm_c'=>Order::checkConfirmation($this->order_no,'C'),
            'confirm_d'=>Order::checkConfirmation($this->order_no,'D'),
            'status'=>$this->status,
            'sector'=>$this->sector,
            'ending_time'=>Carbon::parse($this->ending_time)->toTimeString(),
            'ending_date'=>Carbon::parse($this->ending_date)->toDateString(),
            'search_name'=>$this->order_no.'|'.$this->customer_name.'|'.$this->shp_name,
            'lines'=>$this->whenLoaded('lines'),
            'confirmations_count'=>$this->whenCounted('confirmations'),
            'assignmentLines_count'=>$this->whenCounted('assignmentLines'),
            'assignmentLines'=>$this->whenLoaded('assignmentLines'),


        ];

    }

}
