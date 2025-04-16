<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\EventLogController;

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

Route::get('/', function () {
    return view('welcome');
});


Route::prefix('event-logs')->group(function () {
    Route::get('/', [EventLogController::class, 'index']);
    Route::post('/storeEvent', [EventLogController::class, 'store']);
});
