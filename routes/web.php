<?php

use App\Http\Controllers\{ProfileController,OrderController};
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use illuminate\Http\Request;
use Inertia\Inertia;

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

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



Route::middleware('auth')->group(function () {

    //Order Controller functions
        Route::get('/scanner',[OrderController::class,'scan'])->name('scanner');
        // Route::get('/order/{id}', [OrderController::class, 'show'])->name('order.show');
        Route::get('/order/{id}/{part}', [OrderController::class, 'show'])->name('order.show');
        Route::get('/total',fn()=>inertia('Orders/List'))->name('total');
        Route::post('/execute',[OrderController::class,'execute'])->name('order.execute');
        Route::get('parts/{id}',[OrderController::class,'selectOrderPart'])->name('order.parts');
        Route::get('/confirm/{id}',[OrderController::class,'confirm'])->name('order.confirm');
        // Route::post('/getOrder', fn(Request $request)=>dd($request->all()));
        // Route::get('/sound/beep-07a.mp3');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
