<?php

namespace App\Http\Controllers;

use App\Models\{Order,Assignment};
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    
    //list all assignments
    public function index(Request $request)
    {
      
       //list orders with parts and assignments

        $queryBuilder = Order::current()
                             ->select($columns);


        $searchParameter = $request->has('search')?$request->search:'';
        $searchColumns = ['customer_name', 'shp_name','order_no'];
        $strictColumns = [];
        $relatedModels = [
                            'relatedModel1' => ['related_column1', 'related_column2'],
                            'relatedModel2' => ['related_column3'],
                         ];



        $searchService = new SearchQueryService($queryBuilder, $searchParameter, $searchColumns, [], []);
        $orders = $searchService
                  ->with(['confirmations','assignments','lines']) 
                  ->search();

     }
    


}
