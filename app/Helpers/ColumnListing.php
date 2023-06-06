<?php
namespace App\Helpers;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


class ColumnListing {


    public $tableName = 'your_table_name';
    public $columns=[];



    public function __construct($tableName)

    {
      $this->tableName=$tableName;
      $this->columns = Schema::getColumnListing($tableName);
      $this->columns = array_diff($this->columns, ['id','created_at','updated_at']);

   }


   public function getColumns() :array
   {

        $columnTypes = [];

        foreach ($this->columns as $column)
        {
            $columnTypes[$column] =
               ['name'=>$column,
                'type'=>Schema::getColumnType($this->tableName, $column),
                'default_values'=> (DB::table($this->tableName)
                                       ->select($column)
                                       ->distinct()
                                       ->count()>10
                                       )?'':DB::table($this->tableName)
                                       ->select($column)
                                       ->distinct()->get()
               ];
        }

        return $columnTypes;
   }


}


?>
