<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Http\Resources\RoleResource;
use App\Services\SearchQueryService;

class RoleController extends Controller
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
        //list all the Roles
        $query= Role::latest();
        $searchParameter = $request->has('search')?$request->search:'';
        $searchColumns = ['name'];
        $strictColumns = [];
        $relatedModels = [
                            // 'relatedModel1' => ['related_column1', 'related_column2'],
                            // 'relatedModel2' => ['related_column3'],
                         ];



        $searchService = new SearchQueryService($query, $searchParameter, $searchColumns, $strictColumns, $relatedModels);
        // dd($searchService);
        $roles = $searchService
                // ->with(['Roles','roles']) // Example of eager loading related models
                ->search();
        $rows=$request->has('rows')?$request->rows:10;

        $roles= RoleResource::collection($roles->paginate($rows));

        return inertia('Role/List',compact('roles'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        Role::create(['name'=>$request->name,'guard_name'=>'web']);
        return redirect(route('roles.index'));
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
               Role::firstWhere('name',$id)?->update(['name'=>$request->name,'guard_name'=>'web']);
        return redirect (route('roles.index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role=Role::find($id);
        $role->delete();
        return redirect(route('roles.index'));
    }

}
