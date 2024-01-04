<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vessel extends Model
{
    use HasFactory;

    protected $guarded=['id'];

    public function loadingSession()
    {
        return $this->belongsTo(LoadingSession::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class,'order_no','order_no');
    }

    public static function checkRecordExists($searchCriteria)
    {
        // Build the query dynamically based on the search criteria
        $query = Vessel::query();

        foreach ($searchCriteria as $key => $value) {
            $query->where($key, $value);
        }

        // Check if any records match the criteria
        return $query->exists();
    }

    public static function findRecord($searchCriteria)
    {
        // Build the query dynamically based on the search criteria
        $query = Vessel::query();

        foreach ($searchCriteria as $key => $value) {
            $query->where($key, $value);
        }

        // Retrieve the first matching record
        return $query->first();
    }

    public function logs()
    {
        return $this->hasMany(VesselLog::class);
    }




}
