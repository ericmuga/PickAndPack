<?php

use App\Http\Controllers\{ConfirmationController,
                          DashboardController,
                          ItemController,
                          LineController,
                          LinePrepackController,
                          ProfileController,
                          OrderController,
                          PackingController,
                          PickController,
                          PrepackController,
                          StockController,
                         AssemblyController,
                         AssignmentController,
    LoadingSessionController,
    PackingSessionController,
    PermissionController,
    RoleController,
    UserController,
  VehicleController,
    VesselController,
    ApiController,
    PackingSessionLineController,
    PackingVesselController
};
use Illuminate\Foundation\Application;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\{Route,Auth};
use illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Resources\{OrderResource};
use App\Models\{Line,LinePrepack, LoadingSession, Order, PackingSession, Permission, Prepack, Stock, Vehicle};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
 Route::get('/',fn()=>Auth::check()?redirect('dashboard'):redirect('login'));


// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });



// Route::get('/fetch-and-save', [ApiController::class, 'fetchDataAndSave'])->name('fetch');
Route::get('/makeApiCall', [ApiController::class, 'makeCall'])->name('makeCall');

Route::middleware('auth')->group(function () {
Route::get('registry/download',[ConfirmationController::class, 'download'])->name('registry.download');

///////////////////////////////ACL routes/////////////////////////////////

      Route::resource('permissions',PermissionController::class);
      Route::get('permissionDownload',[PermissionController::class,'download'])->name('permissions.download');
      Route::resource('roles',RoleController::class);
      Route::get('roleDownload',[RoleController::class,'download'])->name('roles.download');

///////////////////////end of ACL routes


////////////////////////////////Loading Routes ///////////////////////////////////////////////////
      Route::resource('loadingSession',LoadingSessionController::class);
      Route::get('/loadSession',[LoadingSessionController::class,'loadSession'])->name('loadSession');
      Route::get('load/{id}', [LoadingSessionController::class,'load'])->name('load');
      Route::get('loadVessel', [LoadingSessionController::class,'loadVessel'])->name('loadVessel');
      Route::post('load',[LoadingSessionController::class,'load'])->name('load.add');
      Route::post('loadingSheet',[LoadingSessionController::class,'loadingSheet'])->name('loadingSheet');


      Route::resource('users', UserController::class);
      Route::get('userDownload',[UserController::class,'download'])->name('users.download');



      Route::resource('vehicles',VehicleController::class);
      Route::get('vehicleDownload',[VehicleController::class,'download'])->name('vehicles.download');
      Route::resource('vessels',VesselController::class);
      Route::post('voidVessel',[VesselController::class,'voidVessel'])->name('vessels.void');

      Route::post('uploadVessel',[VesselController::class, 'upload'])->name('vessels.upload');
      Route::get('vesselDownload',[VesselController::class,'download'])->name('vessels.download');
///////////////////////////end of loading routes//////////////////////////////



       Route::post('/generate-pdf',[PackingController::class,'printLabel'])->name('packing.printLabel');
        Route::get('assignment/assignmentStore',[AssignmentController::class,'getPending']);
        Route::resource('assignment',AssignmentController::class);


        Route::post('download/linePrepacks',[LinePrepackController::class,'export'])->name('linePrepacks.download');
        Route::resource('linePrepacks',LinePrepackController::class);



        /////////////////////Assembly Routes //////////////////////
         ///list orders ready for assembly
        Route::resource('assembly',AssemblyController::class);
        Route::get('assemble/order', [AssemblyController::class, 'assembleOrder'])->name('assemble.order');

         /////////////packing Routes///////////////////

        Route::resource('packing',PackingController::class);
        Route::get('pack/order', [PackingController::class, 'packOrder'])->name('pack.order');


        ////////////////packing session routes/////////////////////////////
        Route::resource('packingSession',PackingSessionController::class);
        Route::post('getLines/packingSessions',[PackingSessionController::class,'getLines'])->name('packingSession.getLines');
        Route::post('closePackingSession',[PackingSessionController::class,'closePacking'])->name('packingSession.close');
        Route::resource('packingVessel',PackingVesselController::class);
        Route::get('packingSessionExport', [PackingSessionController::class, 'export'])->name('packingSessions.export');

        Route::post('getOrderParts',[PackingSessionController::class,'getOrderParts'])->name('packingSession.getOrderParts');


        Route::resource('packingSessionLine',PackingSessionLineController::class);


        // Route::get('/dashboard',[DashboardController::class,'dashboard'] )->name('dashboard');
        Route::get('/dashboard',fn()=>inertia('Dashboards/Landing'))->name('dashboard');


        Route::get('/refresh',[OrderController::class,'refresh'] )->name('refresh');
        Route::get('/OrderRefresh',[ConfirmationController::class,'refresh'] )->name('orderRefresh');
        Route::get('/scanner',[OrderController::class,'scan'])->name('scanner');
        Route::get('orders/download/', [OrderController::class, 'export'])->name('orders.export');


        // Route::get('orders/assemble/{part?}/{sector?}/{sp_code?}',[OrderController::class, 'assemble'])->name('orders.assemble');
        Route::get('allocate/orders',[OrderController::class, 'assemble'])->name('orders.lines');
        Route::post('orders/prepack',[OrderController::class,'prepack'])->name('orders.prepack');


        Route::get('packingSession/getLastVessel/{id}',[PackingSessionController::class, 'getLastVessel'])->name('session.getLastVessel');

        Route::get('/order/{id}/{part}', [OrderController::class, 'show'])->name('order.show');
        Route::get('/confirmations', [OrderController::class, 'index'])->name('order.list');

        Route::get('line/prepacks',fn(Request $request)=>Line::where('line_no')->prepacks()->get());
        Route::post('line/add',[LineController::class, 'add'])->name('prepacks.add');
        Route::resource('lines',LineController::class);
        Route::get('history/lines',[LineController::class, 'history'])->name('lines.history');
        Route::post('filter/lines',[LineController::class,'filterd'])->name('lines.filtered');


        Route::resource('prepacks',PrepackController::class);

        Route::get('/total',fn()=>inertia('Orders/List'))->name('total');
        Route::post('/execute',[OrderController::class,'execute'])->name('order.execute');
        Route::get('parts/{id}',[OrderController::class,'selectOrderPart'])->name('order.parts');
        // Route::get('/confirm/{id}',[OrderController::class,'confirm'])->name('order.confirm');
        // Route::post('/getOrder', fn(Request $request)=>dd($request->all()));
        // Route::get('/sound/beep-07a.mp3');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

        //updating that a part has been printed
        Route::post('/updatePart',[OrderController::class ,'updatePart'])->name('order.updatepart');
        Route::post('/order/confirm',[OrderController::class ,'confirmation'])->name('order.confirm');
        Route::post('/order/filter',[OrderController::class ,'filter'])->name('order.filter');
        Route::get('/order/filter',[OrderController::class ,'filter'])->name('order.filters');




        Route::get('/order/pack',[OrderController::class ,'pack'])->name('order.pack');


        Route::post('/order/all', [OrderController::class, 'pack'])->name('order.listing');
        Route::post('/order/scanItems', [OrderController::class, 'scanItems'])->name('order.scanItems');
        Route::post('getOrderPart',[OrderController::class,'getOrderPart'])->name('getOrderPart');


        // Route::get('pick',[OrderController::class,'pick'])->name('order.pick');

        //picks
        Route::resource('picks',PickController::class);

        //stocks
        Route::resource('stocks',StockController::class);

        //confirmations
        Route::resource('confirmations',ConfirmationController::class);
        Route::get('exportConfirmations',[ConfirmationController::class,'export'])->name('export.confirmations');
       //API
       //API

       //---------------Items ------------------------//
       Route::get('items/download',[ItemController::class,'download'])->name('items.download');

       Route::resource('items',ItemController::class);



       Route::get('/orders',fn()=>OrderResource::collection(Order::select('order_no','customer_name','shp_name','shp_date')
                                                                 ->paginate(15)->withQuerystring()
                                                            )
                  )->name('orders.get');

    });


require __DIR__.'/auth.php';
