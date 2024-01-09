<?php

namespace App\Http\Controllers;

use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;
use App\Http\Resources\{UserResource,UserApiResource};
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\SearchQueryService;

class UserController extends Controller
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


    public function fetchUsers()
    {

        ///this will responc to an API call and return users with name,email,and roles object
       $users= UserApiResource::collection(User::with(['roles','permissions'])->get());

       return response(compact('users'),200,[]);

    }


    public function index(Request $request)
    {
        //list all the users
        $query= User::query();
        $searchParameter = $request->has('search')?$request->search:'';
        $searchColumns = ['name','email'];
        $strictColumns = [];
        $relatedModels = [
                            // 'relatedModel1' => ['related_column1', 'related_column2'],
                            // 'relatedModel2' => ['related_column3'],
                         ];



        $searchService = new SearchQueryService($query, $searchParameter, $searchColumns, $strictColumns, $relatedModels);
        // dd($searchService);
        $users = $searchService
                ->with(['permissions','roles']) // Example of eager loading related models
                ->search();
        $rows=$request->has('rows')?$request->rows:10;


    //   dd($users);

        $users= UserResource::collection($users->paginate($rows));
        $roles= RoleResource::collection(Role::all());
        $permissions=PermissionResource::collection(Permission::all());

        return inertia('User/List',compact('users','roles','permissions'));

    }


    public function store(Request $request)
    {
        //
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
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
        //
        //  dd($request->all());gi
         $user=User::firstWhere('email',$request->email);
         $user->password=bcrypt($request->pass);
         $user->save();
         $user->syncRoles($request->roles);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
