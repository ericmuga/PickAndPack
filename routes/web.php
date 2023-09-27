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
                         AssemblyController
                       };
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\{Route,Auth};
use illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Resources\{OrderResource};
use App\Models\{Line,LinePrepack,Order,Prepack, Stock};

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





Route::middleware('auth')->group(function () {

        Route::post('download/linePrepacks',[LinePrepackController::class,'export'])->name('linePrepacks.download');
        Route::resource('linePrepacks',LinePrepackController::class);



        /////////////////////Assembly Routes //////////////////////
         ///list orders ready for assembly
        Route::resource('assembly',AssemblyController::class);
         ///select order for assembly
        Route::get('assemble/order', [AssemblyController::class, 'assembleOrder'])->name('assemble.order');


        // Route::get('stores/form',fn()=>inertia('Stores/Form'))->name('stores.form');
        // Route::resource('packing',PackingController::class);
        Route::get('packing',[PackingController::class,'index'])->name('packing.index');
        Route::get('packing/pack',[PackingController::class, 'pack'])->name('packing.pack');
        Route::post('packing/closeAssembly',[PackingController::class,'closePacking'])->name('packing.close');
        // Route::post('/packing/scanItems', [PackingController::class, 'scanItems'])->name('packing.scanItems');
        // Route::get('/packing/scanItems', [PackingController::class, 'scanItems'])->name('packing.scanItemsGET');


        // Route::get('linePrepacks',[LinePrepackController::class,'index'])->name('slinePrepacks.index');
    //Order Controller functions
        Route::get('/dashboard',[DashboardController::class,'dashboard'] )->name('dashboard');
        // Route::get('/searchStocks',[DashboardController::class,'dashboard'] )->name('searchStocks');


        Route::get('/refresh',[OrderController::class,'refresh'] )->name('refresh');
        Route::get('/scanner',[OrderController::class,'scan'])->name('scanner');
        Route::get('orders/download/', [OrderController::class, 'export'])->name('orders.export');
        // Route::get('orders/assemble/{part?}/{sector?}/{sp_code?}',[OrderController::class, 'assemble'])->name('orders.assemble');
        Route::get('allocate/orders',[OrderController::class, 'assemble'])->name('orders.lines');
        Route::post('orders/prepack',[OrderController::class,'prepack'])->name('orders.prepack');
        
        // Route::get('orders/prepacked',[OrderController::class,'orderPrepacks'])->name('orders.prepacks');

        //Prepacked Orders



        // Route::get('orders/assemble',[OrderController::class, 'assemble'])->name('orders.lines');

        // Route::get('/order/{id}', [OrderController::class, 'show'])->name('order.show');
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
