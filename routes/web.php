<?php

use Illuminate\Support\Facades\Route;



// Controllers
use App\Http\Controllers\VehicleController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/




Route::get('/', [VehicleController::class, 'index']);
Route::get('/api/search', [VehicleController::class, 'search'])->name('vehicles.search');
Route::post('/vehicles/filter', [VehicleController::class, 'filter'])->name('vehicles.filter');