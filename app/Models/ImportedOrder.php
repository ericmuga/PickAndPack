<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportedOrder extends Model
{
    use HasFactory;

    protected $table ='imported_orders';
    // protected $connection ='sales2';
    protected $guarded=['id'];

}
