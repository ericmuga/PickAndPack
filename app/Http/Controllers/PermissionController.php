<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Http\Resources\PermissionResource;
use App\Services\SearchQueryService;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request)
    {
        dd('under maintenance');
        // return Excel::download(new ConfirmationExport([$request->from,$request->to]), 'confirmations.xlsx');
        // return Excel::download(new ConfirmationExport(['2023-06-09','2023-06-09']), 'confirmations.xlsx');
    }




    public function index(Request $request)
    {
        //list all the Permissions
        $query= Permission::latest();
        $searchParameter = $request->has('search')?$request->search:'';
        $searchColumns = ['name'];
        $strictColumns = [];
        $relatedModels = [
                            // 'relatedModel1' => ['related_column1', 'related_column2'],
                            // 'relatedModel2' => ['related_column3'],
                         ];



        $searchService = new SearchQueryService($query, $searchParameter, $searchColumns, $strictColumns, $relatedModels);
        // dd($searchService);
        $Permissions = $searchService
                // ->with(['permissions','roles']) // Example of eager loading related models
                ->search();
        $rows=$request->has('rows')?$request->rows:10;


    //   dd($Permissions);

        $permissions= PermissionResource::collection($Permissions->paginate($rows));
        // $roles= RoleResource::collection(Role::all());
        // $permissions=PermissionResource::collection(Permission::all());

        return inertia('Permission/List',compact('permissions'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        Permission::create(['name'=>$request->name,'guard_name'=>'web']);
        return redirect(route('permissions.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
               Permission::firstWhere('name',$id)?->update(['name'=>$request->name,'guard_name'=>'web']);
        return redirect (route('permissions.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $Permission=Permission::find($id);
        $Permission->delete();
        return redirect(route('permissions.index'));
    }
    
}
